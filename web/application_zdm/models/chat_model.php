<?php
/**
 * department_acl_model
 *
 * @author  liuchenlin
 * @time    2017-12-11
 */

class chat_model extends MY_Model
{
    public function __construct() {
        $this->db = $this->load->mysql("sos");
    }

    /**
    * 添加群组记录
     */
    public function add_chat_record($name,$owner,$chatid,$openConversationId,$conversationTag,$app_id=1)
    {
        if(!$name || !$owner){
            return false;
        }

        $data['name'] = $name;
        $data['owner'] = $owner;
        $data['chatid'] = $chatid;
        $data['openConversationId'] = $openConversationId;
        $data['conversationTag'] = $conversationTag;
        $data['app_id'] = $app_id;
        $data['webhook'] = '';
        $data['create_time'] = time();
        $data['update_time'] = time();
        $res = $this->db->insert('sos_chat_info', $data);
        return $res;
    }

     /**
     * 获取信息
     */
    public function get_chat_info($id=1){
        $sql  = ' 
                select
                    id,owner,chatid,openConversationId,conversationTag,webhook
                from 
                    sos_chat_info 
                where 
                    sos_chat_info.deleted=0
                    and id = ?
                 ';
                 $where['id'] = $id;
        $res = $this->db->prepare_query($sql,$where,['get'=>'row']);
        return $res;
    }
    /**
     * 更新钉钉信息
     */
    public function update_chat_info($id,$data){
        if (!is_array($data) || count($data) == 0 || intval($id)==0) {
            return false;
        }

        $data['update_time'] = time();
        $where_conds = [
            '`id`=' => $id,
        ];
        $result = $this->db->update('`sos_chat_info`', $data, $where_conds);
        if(FALSE === $result){
            return FALSE;
        }
        return $result;
    }
    
    
    

}