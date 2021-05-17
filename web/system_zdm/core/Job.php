<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * ZDM Job Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/config.html
 */
class CI_Job {

    public $mode = 'free'; #free q2q q2h h2d  
    #配置常量放在这
    public $option = [
        'mode' => 'free',
        'tube_id' => 0, #q2q 拉取的队列tube尾号(用于多进程) 
        'queue_id' => 0, #q2q 拉取的队列尾号(0和1 1为灾备)
        'job_name' => '', #q2h 作业名
        'event_id_str' => '', #q2h 事件id
        'proc_num' => 1, #q2h 作业开启的进程数
        'tail_id' => 0, #q2h 当前进程序号(从0开始)
        'last_pull_id' => 0, #q2h 从事件表的这个id开始拉取
        'last_handle_id' => 0, #q2q 从handle预处理表的这个id读取
        'last_push_id' => 0, #h2d 最后一次推送的id(只为查询handleSQL提速,没有逻辑意义)
        'limit' => 100, #一次读取的数据行数
        'db_error_count_limit' => 3, #数据库报错大于此值程序强制退出
        'mq_error_count_limit' => 3, #高速队列报错大于此值程序强制退出
        'loops_interval' => 0, #循环间隔时间(毫秒)
    ];

    #普通变量放在这
    public $var = [
        'db_error_count' => 0, #数据库连续报错次数
        'db_error_count2' => 0, #数据库连续报错次数
        'db_error_count3' => 0, #数据库连续报错次数
        'db_error_count4' => 0, #数据库连续报错次数
        'mq_error_count' => 0, #告诉队列连续报错次数
        'message_queue_id' => null, #高速队列里的元素id
        'loops_result' => true, #每次循环处理结果是否成功
        'jump' => '', #循环中 标记continue或者break以及break 2
        'is_empty' => false, #控制empty只打印一次
    ];

    #服务变量放在这
    public $service = [
        'message_queue' => null,
            #'comment_db' => $xyz,
            #'comment_redis' => $hij,
    ];

    #数据变量放在这
    public $data = [
        'message_queue' => [], #从队列取出的数据
        'row_data' => [], #待加工的业务逻辑数据
        'rows_data' => [], #待加工的业务逻辑数据(多行)
    ];

    #临时变量放在这 每个循环会清掉;
    public $temp = [];

    function __construct($option = []) {
        $this->update_option($option);
        $this->change_mode($this->option['mode']);
    }

    function dt() {
        return date('Y-m-d H:i:s');
    }

    function update_option($option = []) {
        $this->option = array_merge($this->option, $option);
    }

    function change_mode($mode = 'free') {
        $this->mode = $mode;
    }

    function run() {
        switch ($this->mode) {
            case 'q2q': #从高速队列拉取到事件队列
                $this->run_pull_q2q();
                break;
            case 'q2h': #从事件队列拉取到handle
                $this->run_pull_q2h();
                break;
            case 'h2d': #从handle推送到目标数据
                $this->run_push_h2d();
                break;
            case 'free':
            default:
        }
    }

    /**
     * 从高速队列拉取数据到事件库
     */
    function run_pull_q2q() {

        $result = $this->_before_loops(); ########################################################################

        if (false === $result) {
            return;
        }

        while (true) {
            #取队列数据
            $this->var['jump'] = '';

            $this->_on_loops_fetch_data(); ############################################################

            if ($this->var['jump'] == 'continue') {
                continue;
            } elseif ($this->var['jump'] == 'break') {
                break;
            }

            #队列取出的数据
            $this->var['message_queue_id'] = $this->data['message_queue']['id'];
            $this->data['row_data'] = json_decode($this->data['message_queue']['body'], true);
            print "{$this->dt()} job_id:{$this->var['message_queue_id']}\n";

            #处理业务逻辑
            $this->var['jump'] = '';

            $this->_on_loops_after_fetch_data(); ###########################################################

            if ($this->var['jump'] == 'continue') {
                continue;
            } elseif ($this->var['jump'] == 'break') {
                break;
            }

            if ($this->var['loops_result']) {
                #本次业务处理成功
                $this->var['jump'] = '';

                $this->_on_loops_after_handle_success(); ###################################################

                if ($this->var['jump'] == 'continue') {
                    continue;
                } elseif ($this->var['jump'] == 'break') {
                    break;
                }
            } else {
                #本次业务处理失败
                $this->var['jump'] = '';

                $this->_on_loops_after_handle_failed(); ###################################################

                if ($this->var['jump'] == 'continue') {
                    continue;
                } elseif ($this->var['jump'] == 'break') {
                    break;
                }
            }

            #注销临时变量
            $this->temp = null;
            usleep($this->option['loops_interval']);
        }
    }

    /**
     * 从事件表拉取数据到handle表
     */
    function run_pull_q2h() {

        $result = $this->_before_loops(); ########################################################################

        if (false === $result) {
            return;
        }
        
        $sleep_time = '20000'; #每秒处理50个 (压测的时候 每秒70+ 偶尔会有漏掉一个的情况)

        while (true) {
            #批量获取事件
            $this->var['jump'] = '';

            $this->_on_loops_fetch_data(); ############################################################

            if ($this->var['jump'] == 'continue') {
                continue;
            } elseif ($this->var['jump'] == 'break') {
                break;
            }

            foreach ($this->data['rows_data'] as $row) { #逐一处理取出的事件
                $this->data['row_data'] = $row;
                print "{$this->dt()} queue_id:{$this->data['row_data']['id']}\n";

                #处理业务逻辑
                $this->var['jump'] = '';

                $this->_on_loops_after_fetch_data(); ###########################################################

                if ($this->var['jump'] == 'continue') {
                    continue;
                } elseif ($this->var['jump'] == 'break') {
                    break;
                } elseif ($this->var['jump'] == 'break 2') {
                    break 2;
                }

                if ($this->var['loops_result']) {
                    #本次业务处理成功 更新last_pull_id
                    $this->var['jump'] = '';

                    $this->_on_loops_after_handle_success(); ###################################################
                    
                    if ($this->var['jump'] == 'continue') {
                        continue;
                    } elseif ($this->var['jump'] == 'break') {
                        break;
                    } elseif ($this->var['jump'] == 'break 2') {
                        break 2;
                    }
                    usleep($sleep_time);
                } else {
                    #本次业务处理失败
                    $this->var['jump'] = '';

                    $this->_on_loops_after_handle_failed(); ###################################################

                    if ($this->var['jump'] == 'continue') {
                        continue;
                    } elseif ($this->var['jump'] == 'break') {
                        break;
                    } elseif ($this->var['jump'] == 'break 2') {
                        break 2;
                    }
                }
            }


            #注销临时变量
            $this->temp = null;
            usleep($this->option['loops_interval']);
        }
    }

    function run_push_h2d() {
        $result = $this->_before_loops(); ########################################################################

        if (false === $result) {
            return;
        }

        while (true) {
            #批量取handle待处理的数据
            $this->var['jump'] = '';

            $this->_on_loops_fetch_data(); ############################################################

            if ($this->var['jump'] == 'continue') {
                continue;
            } elseif ($this->var['jump'] == 'break') {
                break;
            }

            foreach ($this->data['rows_data'] as $row) { #逐一推送 推送成功将handle表的状态置为1 失败置为-1
                $this->data['row_data'] = $row;
                print "{$this->dt()} handle_id:{$this->data['row_data']['id']}\n";

                #处理业务逻辑
                $this->var['jump'] = '';

                $this->_on_loops_after_fetch_data(); ###########################################################

                if ($this->var['jump'] == 'continue') {
                    continue;
                } elseif ($this->var['jump'] == 'break') {
                    break;
                } elseif ($this->var['jump'] == 'break 2') {
                    break 2;
                }

                if ($this->var['loops_result'] !== false) {
                    #本次业务处理成功 修改handle表状态
                    $this->var['jump'] = '';

                    $this->_on_loops_after_handle_success(); ###################################################

                    if ($this->var['jump'] == 'continue') {
                        continue;
                    } elseif ($this->var['jump'] == 'break') {
                        break;
                    } elseif ($this->var['jump'] == 'break 2') {
                        break 2;
                    }
                } else {
                    #本次业务处理失败 修改handle表状态
                    $this->var['jump'] = '';

                    $this->_on_loops_after_handle_failed(); ###################################################

                    if ($this->var['jump'] == 'continue') {
                        continue;
                    } elseif ($this->var['jump'] == 'break') {
                        break;
                    } elseif ($this->var['jump'] == 'break 2') {
                        break 2;
                    }
                }
            }


            #注销临时变量
            $this->temp = null;
            usleep($this->option['loops_interval']);
        }
    }

    protected function _before_loops() {
        if ('q2h' == $this->mode) {
            #处理last_pull_id (后面处理event_queue的主键检索条件)
            $this->option['last_pull_id'] = (int) abs($this->option['last_pull_id']);
            #读取数据库中的last_pull_id
            $db_last_pull_id = $this->service['handle_db']->get_last_pull_id($this->option['job_name'], $this->option['proc_num'], $this->option['tail_id']);
            if (false === $db_last_pull_id) {
                #数据库查询失败
                return false;
            } elseif (is_array($db_last_pull_id) && empty($db_last_pull_id)) {
                #db无记录, 初始化db记录
                $result = $this->service['handle_db']->init_job_mark($this->option['job_name'], $this->option['proc_num'], $this->option['tail_id'], $this->option['last_pull_id']);
                if (false === $result) {
                    #数据库操作失败
                    return false;
                }
                return true;
            } else {
                #db有记录
                if (!$this->option['last_pull_id'] > 0) {
                    #如果没有参数指定last_pull_id，使用数据库记录
                    $this->option['last_pull_id'] = $db_last_pull_id;
                }
                return true;
            }
        } elseif ('h2d' == $this->mode) {
            #处理last_push_id (可一直为0,每个循环更新此值起到SQL提速的作用)
            $this->option['last_push_id'] = (int) abs($this->option['last_push_id']);
        }
    }

    /**
     * 循环开始取数据
     */
    protected function _on_loops_fetch_data() {
        if ('q2q' == $this->mode) {
            $this->data['message_queue'] = [];
            try {
                $this->data['message_queue'] = $this->service['message_queue']->reserve();
            } catch (Exception $e) {
                
            }

            $this->_on_loops_fetch_data_over();
        } elseif ('q2h' == $this->mode) {
            $this->data['rows_data'] = $this->service['event_db']->get_limit_queue($this->option['last_pull_id'], $this->option['event_id_str'], $this->option['limit'], $this->option['proc_num'], $this->option['tail_id']);
            $this->_on_loops_fetch_data_over();
        }
    }

    /**
     * 循环取完数据后的错误情况判断
     * @return type
     */
    protected function _on_loops_fetch_data_over() {
        if ('q2q' == $this->mode) {
            #当队列无数据时,reserve()为阻塞状态,直到有新数据插入
            if (empty($this->data['message_queue']) || empty($this->data['message_queue']['id']) || empty($this->data['message_queue']['body'])) {
                #当队列访问失败，睡X秒，当连续失败Y次，程序中断，等待下次启动
                $this->var['mq_error_count'] ++;
                print "{$this->dt()} mq_error_count:{$this->var['mq_error_count']}\n";
                if ($this->var['mq_error_count'] >= $this->option['mq_error_count_limit']) {
                    $this->var['jump'] = 'break';
                    return;
                }
                sleep(2);
                $this->var['jump'] = 'continue';
            } else {
                $this->var['mq_error_count'] = 0;
            }
        } elseif ('q2h' == $this->mode) {
            if (false === $this->data['rows_data']) {
                #查询失败,累加次数,连续失败X次,强制退出
                $this->var['db_error_count'] ++;
                print "{$this->dt()} db_error_count:{$this->var['db_error_count']}\n";
                if ($this->var['db_error_count'] >= $this->option['db_error_count_limit']) {
                    $this->var['jump'] = 'break';
                    return;
                }
                sleep(1);
                $this->var['jump'] = 'continue';
                return;
            } elseif (empty($this->data['rows_data'])) {
                #事件已经拉完,没有结果.睡5s
                $this->var['db_error_count'] = 0;
                if (!$this->var['is_empty']) {
                    print "{$this->dt()} empty!\n";
                    $this->var['is_empty'] = true;
                }
                sleep(5);
                if (date('s') > 48 && date('s') < 55) {
                    #第50左右 退出 等待下次cron执行
                    $this->var['jump'] = 'break';
                } else {
                    $this->var['jump'] = 'continue';
                }
                return;
            }
            print "{$this->dt()} new_loop:{$this->option['last_pull_id']}\n";
            $this->var['is_empty'] = false;
            $this->var['db_error_count'] = 0;
        } elseif ('h2d' == $this->mode) {
            if (false === $this->data['rows_data']) {
                #查询失败,累加次数,连续失败X次,强制退出
                $this->var['db_error_count'] ++;
                print "{$this->dt()} db_error_count:{$this->var['db_error_count']}\n";
                if ($this->var['db_error_count'] >= $this->option['db_error_count_limit']) {
                    $this->var['jump'] = 'break';
                    return;
                }
                sleep(1);
                $this->var['jump'] = 'continue';
                return;
            } elseif (empty($this->data['rows_data'])) {
                #handle已经推完,没有待处理数据.睡5s,
                $this->var['db_error_count'] = 0;
                if (!$this->var['is_empty']) {
                    print "{$this->dt()} empty!\n";
                    $this->var['is_empty'] = true;
                }
                sleep(5);
                #重置last_push_id
                $this->option['last_push_id'] = 0;
                $this->var['jump'] = 'continue';
                return;
            }
            $this->var['is_empty'] = false;
            $this->var['db_error_count'] = 0;
        }
    }

    /**
     * 循环中 取完数据处理过程
     */
    protected function _on_loops_after_fetch_data() {
        
    }

    /**
     * 循环中 数据处理成功后要做的事
     */
    protected function _on_loops_after_handle_success() {
        if ('q2q' == $this->mode) {
            $this->var['db_error_count'] = 0;
            #高速队列中的数据同步后删除
            $this->service['message_queue']->delete($this->var['message_queue_id']);
        } elseif ('q2h' == $this->mode) {
            $this->var['db_error_count2'] = 0;
            #更新last_pull_id
            $this->option['last_pull_id'] = $this->data['row_data']['id'];
            $result = $this->service['handle_db']->update_job_mark($this->option['job_name'], $this->option['proc_num'], $this->option['tail_id'], $this->option['last_pull_id']);
            if (false === $result) {
                print "{$this->dt()} update jobmark failed:last_pull_id:{$this->option['last_pull_id']}\n";
                $this->var['jump'] = 'break 2';
            }
        }
    }

    /**
     * 循环中 数据处理失败后要做的事
     */
    protected function _on_loops_after_handle_failed() {
        if ('q2q' == $this->mode) {
            #有错就跳出
            print "{$this->dt()} loops_result error\n";
            $this->var['jump'] = 'break';
            sleep(1);
        } elseif ('q2h' == $this->mode) {
            #错误数+1
            $this->var['db_error_count2'] ++;
            print "{$this->dt()} insert_error_count:{$this->var['db_error_count2']}\n";
            if ($this->var['db_error_count2'] >= $this->option['db_error_count_limit']) {
                #连续错误数达到阀值强制程序中断
                $this->var['jump'] = 'break 2';
            }
            #退出小循环
            $this->var['jump'] = 'break';
        }
    }

    /**
     * __get
     *
     * Allows models to access CI's loaded classes using the same
     * syntax as controllers.
     *
     * @param	string
     * @access private
     */
    function __get($key) {
        $CI = & get_instance();
        return $CI->$key;
    }

}
