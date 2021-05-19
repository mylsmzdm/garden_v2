<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * User: quguangke 拷贝 来自 图片服务项目
 *
 * @property user_rbac_api user_rbac_api
 * Date: 2016/12/28
 * Time: 16:10
 */
class User_rbac_biz extends CI_Biz{
    function __construct() {
        parent::__construct();
        $this->load->model('manager/user_rbac_api');
        $this->load->library('cas');
    }

    /**
     * 基于CAS的账号登录
     * getUserRbacWithId
     * @author liangdong@smzdm.com
     * @param $appCode
     * @param $treelike
     * @param $userId
     */
    public function getUserRbacWithId($appCode, $treelike, $userId) {
        $result=['error_code'=>0,'error_msg'=>'','data'=>''];

        if(empty($appCode)){
            $result['error_code']='2';
            $result['error_msg']='项目ID不得为空';
            goto RESULT;
        }

        // 获取用户信息
        $userinfo = $this->user_rbac_api->get_user($userId);
        if(empty($userinfo)){
            $result['error_code']='3';
            $result['error_msg']='获取用户信息失败';
            goto RESULT;
        }

        // 获取用户权限
        // $userPower=$this->user_rbac_api->get_user_power($userId, $appCode,$treelike);
        $userPower= json_decode('{"error":"","result":{"permissions":["bgm-ehr.performance-manage","bgm-ehr.system-manage.acl-manage","bgm-ehr.employee-manage","bgm-ehr.department-manage","bgm-ehr.employee-manage.staff.view","bgm-ehr.employee-manage.recruit.add","bgm-ehr.employee-manage.staff.edit","bgm-ehr.department-manage.add","bgm-ehr.department-manage.edit","bgm-ehr.archive-manage","bgm-ehr.vacation_stats","bgm-ehr.vacation_stats.view","bgm-ehr.vacation_stats.edit","bgm-ehr.salary.import","bgm-ehr.salary","bgm-ehr.archive-manage.archive-list","bgm-ehr.archive-manage.contract-template","bgm-ehr.archive-manage.archive-list.view","bgm-ehr.archive-manage.archive-list.edit","bgm-ehr.archive-manage.contract-template.edit","bgm-ehr.employee-manage.recruit.delete","bgm-ehr.employee-manage.staff.export","bgm-ehr.employee-manage.staff.export-select","bgm-ehr.department-manage.delete","bgm-ehr.department-manage.sort","bgm-ehr.archive-manage.archive-list.export-all","bgm-ehr.archive-manage.archive-list.export-select","bgm-ehr.archive-manage.contract-template.export","bgm-ehr.archive-manage.contract-template.delete","bgm-ehr.vacation_stats.import","bgm-ehr.vacation_stats.update","bgm-ehr.vacation_stats.export-all","bgm-ehr.vacation_stats.export-select","bgm-ehr.system-manage","bgm-ehr.standby-manage.list","bgm-ehr.standby-manage.set","bgm-ehr.standby-manage.set.add","bgm-ehr.standby-manage.set.edit","bgm-ehr.standby-manage.set.delete","bgm-ehr.department-manage.view","bgm-ehr.salary.view","bgm-ehr.employee-manage.staff.adjustment-department","bgm-ehr.employee-manage.staff.trajectory","bgm-ehr.standby-manage","bgm-ehr.welfare-manage.list","bgm-ehr.welfare-manage.type","bgm-ehr.welfare-manage.list.add","bgm-ehr.welfare-manage.list.edit","bgm-ehr.welfare-manage.list.delete","bgm-ehr.welfare-manage.list.export-all","bgm-ehr.welfare-manage.list.export-select","bgm-ehr.welfare-manage.type.add","bgm-ehr.welfare-manage.type.edit","bgm-ehr.welfare-manage.type.delete","bgm-ehr.welfare-manage","bgm-ehr.data.month","bgm-ehr.data.department","bgm-ehr.data.rank","bgm-ehr.data.rank.month","bgm-ehr.data.rank.compare","bgm-ehr.data.sex","bgm-ehr.data.education","bgm-ehr.data.age","bgm-ehr.data.siling","bgm-ehr.data.gongling","bgm-ehr.data.quit","bgm-ehr.welfare-manage.birthday","bgm-ehr.welfare-manage.birthday.view","bgm-ehr.welfare-manage.birthday.export-all","bgm-ehr.welfare-manage.birthday.export-select","bgm-ehr.system-manage.log","bgm-ehr.data","bgm-ehr.performance-manage.mine","bgm-ehr.performance-manage.all","bgm-ehr.performance-manage.transfer","bgm-ehr.performance-manage.rank-list","bgm-ehr.performance-manage.rank-manage","bgm-ehr.performance-manage.mine.view","bgm-ehr.performance-manage.mine.edit","bgm-ehr.performance-manage.mine.delete","bgm-ehr.performance-manage.mine.confirm","bgm-ehr.performance-manage.mine.export","bgm-ehr.performance-manage.transfer.daipingfen.view","bgm-ehr.performance-manage.rank-list.view","bgm-ehr.performance-manage.rank-list.edit","bgm-ehr.performance-manage.rank-list.change","bgm-ehr.performance-manage.rank-manage.set","bgm-ehr.performance-manage.rank-manage.number","bgm-ehr.performance-manage.rank-manage.chart","bgm-ehr.performance-manage.rank-manage.set.view","bgm-ehr.performance-manage.rank-manage.set.edit","bgm-ehr.performance-manage.rank-manage.set.confirm","bgm-ehr.performance-manage.rank-manage.set.apply","bgm-ehr.performance-manage.rank-manage.number.view","bgm-ehr.performance-manage.rank-manage.number.edit","bgm-ehr.performance-manage.rank-manage.number.export","bgm-ehr.performance-manage.rank-list.export-all","bgm-ehr.performance-manage.rank-list.export-select","bgm-ehr.performance-manage.rank-list.confirm-select","bgm-ehr.performance-manage.all.jindu.export-all","bgm-ehr.performance-manage.all.jindu.export-select","bgm-ehr.performance-manage.mine.add","bgm-ehr.performance-manage.rank-sum","bgm-ehr.performance-manage.rank-sum.edit","bgm-ehr.performance-manage.rank-sum.change","bgm-ehr.performance-manage.rank-sum.view","bgm-ehr.performance-manage.rank-sum.publish","bgm-ehr.performance-manage.rank-sum.export-select","bgm-ehr.performance-manage.rank-sum.export-all","bgm-ehr.employee-manage.recruit","bgm-ehr.employee-manage.entry","bgm-ehr.employee-manage.staff","bgm-ehr.employee-manage.quit","bgm-ehr.employee-manage.give_up","bgm-ehr.employee-manage.recruit.view","bgm-ehr.employee-manage.recruit.edit","bgm-ehr.employee-manage.recruit.recruited","bgm-ehr.employee-manage.recruit.export-all","bgm-ehr.employee-manage.recruit.export-select","bgm-ehr.employee-manage.entry.view","bgm-ehr.employee-manage.entry.edit","bgm-ehr.employee-manage.entry.go","bgm-ehr.employee-manage.entry.giveup","bgm-ehr.employee-manage.entry.export-all","bgm-ehr.employee-manage.entry.export-select","bgm-ehr.employee-manage.quit.view","bgm-ehr.employee-manage.quit.export-all","bgm-ehr.employee-manage.quit.export-select","bgm-ehr.employee-manage.give_up.view","bgm-ehr.employee-manage.give_up.export-all","bgm-ehr.employee-manage.give_up.export-select","bgm-ehr.entry_manage","bgm-ehr.entry_manage.xingzheng","bgm-ehr.entry_manage.it","bgm-ehr.entry_manage.head","bgm-ehr.entry_manage.head.view","bgm-ehr.data.hc","bgm-ehr.system-manage.set","bgm-ehr.system-manage.set.view","bgm-ehr.system-manage.set.edit","bgm-ehr.employee-manage.quit.edit","bgm-ehr.performance-manage.all.jindu","bgm-ehr.performance-manage.all.liushui","bgm-ehr.performance-manage.all.jindu.chakan","bgm-ehr.performance-manage.all.jindu.biangeng","bgm-ehr.performance-manage.all.liushui.export-all","bgm-ehr.performance-manage.all.liushui.export-select","bgm-ehr.performance-manage.all.liushui.bohui-select","bgm-ehr.performance-manage.all.liushui.delect-select","bgm-ehr.performance-manage.transfer.export-all","bgm-ehr.performance-manage.transfer.export-select","bgm-ehr.performance-manage.transfer.daipingfen.pingfen","bgm-ehr.performance-manage.transfer.daipingfen.biangeng","bgm-ehr.performance-manage.transfer.daipingfen","bgm-ehr.performance-manage.transfer.daiqueren","bgm-ehr.performance-manage.transfer.daiqueren.bianji","bgm-ehr.performance-manage.transfer.daiqueren.chakan","bgm-ehr.performance-manage.transfer.daiqueren.biangeng","bgm-ehr.performance-manage.transfer.daiqueren.tongguo","bgm-ehr.performance-manage.transfer.daiqueren.bohui","bgm-ehr.performance-manage.rank-list.queren-all","bgm-ehr.performance-manage.rank-sum.queren-select","bgm-ehr.performance-manage.rank-sum.queren-all","bgm-ehr.performance-manage.admin","bgm-ehr.performance-manage.admin.bohui","bgm-ehr.performance-manage.admin.delect","bgm-ehr.performance-manage.admin.chakan","bgm-ehr.assessment","bgm-ehr.assessment.indicators","bgm-ehr.assessment.indicators.list","bgm-ehr.assessment.indicators.save","bgm-ehr.assessment.indicators.delete","bgm-ehr.assessment.university","bgm-ehr.assessment.university.list","bgm-ehr.assessment.university.save","bgm-ehr.assessment.university.delete","bgm-ehr.assessment.weight","bgm-ehr.assessment.weight.list","bgm-ehr.assessment.weight.save","bgm-ehr.assessment.weight.delete","bgm-ehr.assessment.values","bgm-ehr.assessment.values.list","bgm-ehr.assessment.values.save","bgm-ehr.assessment.values.delete","bgm-ehr.assessment.assessment_department_indicators","bgm-ehr.assessment.assessment_department_indicators.getList","bgm-ehr.assessment.assessment_department_indicators.addEdit","bgm-ehr.assessment.assessment_department_indicators.del","bgm-ehr.assessment.assessment_department_indicators.getDetail","bgm-ehr.assessment.remind","bgm-ehr.assessment.remind.list","bgm-ehr.assessment.remind.edit","bgm-ehr.assessment.remind.detail","bgm-ehr.assessment.remind.delete"],"left_menu":[{"path":"\/performance-manage","code":"bgm-ehr.performance-manage","name":"\u7ee9\u6548\u7ba1\u7406123","pid":2820,"id":3450,"type":"modu","childs":[{"path":"\/mine","code":"bgm-ehr.performance-manage.mine","name":"\u6211\u7684\u7ee9\u6548","pid":3450,"id":3451,"type":"modu","childs":[]},{"path":"\/all","code":"bgm-ehr.performance-manage.all","name":"\u5168\u90e8\u7ee9\u6548","pid":3450,"id":3452,"type":"modu","childs":[{"path":"\/","code":"bgm-ehr.performance-manage.all.jindu","name":"\u8003\u6838\u8fdb\u5ea6","pid":3452,"id":4021,"type":"modu","childs":[]},{"path":"\/","code":"bgm-ehr.performance-manage.all.liushui","name":"\u7ee9\u6548\u6d41\u6c34","pid":3452,"id":4022,"type":"modu","childs":[]}]},{"path":"\/transfer","code":"bgm-ehr.performance-manage.transfer","name":"\u7ee9\u6548\u6d41\u8f6c","pid":3450,"id":3453,"type":"modu","childs":[{"path":"\/","code":"bgm-ehr.performance-manage.transfer.daipingfen","name":"\u5f85\u8bc4\u5206","pid":3453,"id":4033,"type":"modu","childs":[]},{"path":"\/","code":"bgm-ehr.performance-manage.transfer.daiqueren","name":"\u5f85\u786e\u8ba4","pid":3453,"id":4034,"type":"modu","childs":[]}]},{"path":"\/rank-list","code":"bgm-ehr.performance-manage.rank-list","name":"\u7b49\u7ea7\u5217\u8868","pid":3450,"id":3454,"type":"modu","childs":[]},{"path":"\/rank-manage","code":"bgm-ehr.performance-manage.rank-manage","name":"\u7b49\u7ea7\u7ba1\u7406","pid":3450,"id":3455,"type":"modu","childs":[{"path":"\/set","code":"bgm-ehr.performance-manage.rank-manage.set","name":"\u7b49\u7ea7\u8bbe\u5b9a","pid":3455,"id":3467,"type":"modu","childs":[]},{"path":"\/number","code":"bgm-ehr.performance-manage.rank-manage.number","name":"\u7b49\u7ea7\u6570\u91cf","pid":3455,"id":3468,"type":"modu","childs":[]},{"path":"\/chart","code":"bgm-ehr.performance-manage.rank-manage.chart","name":"\u5206\u5e03\u5bf9\u6bd4","pid":3455,"id":3469,"type":"modu","childs":[]}]},{"path":"\/rank-sum","code":"bgm-ehr.performance-manage.rank-sum","name":"\u7b49\u7ea7\u6c47\u603b","pid":3450,"id":3656,"type":"modu","childs":[{"path":"\/","code":"bgm-ehr.performance-manage.rank-sum.export-select","name":"\u5bfc\u51fa\u52fe\u9009","pid":3656,"id":3661,"type":"modu","childs":[]}]},{"path":"\/","code":"bgm-ehr.performance-manage.admin","name":"\u7ee9\u6548\u8d85\u7ba1","pid":3450,"id":4043,"type":"modu","childs":[]}]},{"path":"\/employee-manage","code":"bgm-ehr.employee-manage","name":"\u5458\u5de5\u7ba1\u7406","pid":2820,"id":2824,"type":"modu","childs":[{"path":"\/recruit","code":"bgm-ehr.employee-manage.recruit","name":"\u5f85\u62db\u8058","pid":2824,"id":3776,"type":"modu","childs":[]},{"path":"\/entry","code":"bgm-ehr.employee-manage.entry","name":"\u5f85\u5165\u804c","pid":2824,"id":3777,"type":"modu","childs":[]},{"path":"\/staff","code":"bgm-ehr.employee-manage.staff","name":"\u5728\u804c","pid":2824,"id":3778,"type":"modu","childs":[]},{"path":"\/quit","code":"bgm-ehr.employee-manage.quit","name":"\u79bb\u804c","pid":2824,"id":3779,"type":"modu","childs":[]},{"path":"\/give_up","code":"bgm-ehr.employee-manage.give_up","name":"\u653e\u5f03\u5165\u804c","pid":2824,"id":3780,"type":"modu","childs":[]}]},{"path":"\/department-manage","code":"bgm-ehr.department-manage","name":"\u90e8\u95e8\u7ba1\u7406","pid":2820,"id":2823,"type":"modu","childs":[]},{"path":"\/archive-manage","code":"bgm-ehr.archive-manage","name":"\u6863\u6848\u7ba1\u7406","pid":2820,"id":2961,"type":"modu","childs":[{"path":"\/archive-list","code":"bgm-ehr.archive-manage.archive-list","name":"\u6863\u6848\u5217\u8868","pid":2961,"id":2962,"type":"modu","childs":[]},{"path":"\/contract-template","code":"bgm-ehr.archive-manage.contract-template","name":"\u5408\u540c\u6a21\u677f","pid":2961,"id":2963,"type":"modu","childs":[]}]},{"path":"\/vacation-stats","code":"bgm-ehr.vacation_stats","name":"\u5047\u671f\u7ba1\u7406","pid":2820,"id":2904,"type":"modu","childs":[]},{"path":"\/salary","code":"bgm-ehr.salary","name":"\u85aa\u916c\u7ba1\u7406","pid":2820,"id":2903,"type":"modu","childs":[]},{"path":"\/system-manage","code":"bgm-ehr.system-manage","name":"\u7cfb\u7edf\u7ba1\u7406","pid":2820,"id":2821,"type":"modu","childs":[{"path":"\/system_manage\/acl_manage","code":"bgm-ehr.system-manage.acl-manage","name":"\u6743\u9650\u7ba1\u7406","pid":2821,"id":2822,"type":"modu","childs":[]},{"path":"\/log","code":"bgm-ehr.system-manage.log","name":"\u65e5\u5fd7\u7ba1\u7406","pid":2821,"id":3347,"type":"modu","childs":[]},{"path":"\/set","code":"bgm-ehr.system-manage.set","name":"\u5b57\u6bb5\u8bbe\u7f6e","pid":2821,"id":3804,"type":"modu","childs":[]}]},{"path":"\/standby-manage","code":"bgm-ehr.standby-manage","name":"\u5f85\u529e\u7ba1\u7406","pid":2820,"id":3149,"type":"modu","childs":[{"path":"\/list","code":"bgm-ehr.standby-manage.list","name":"\u5f85\u529e\u4e8b\u9879","pid":3149,"id":3150,"type":"modu","childs":[]},{"path":"\/set","code":"bgm-ehr.standby-manage.set","name":"\u5f85\u529e\u8bbe\u7f6e","pid":3149,"id":3151,"type":"modu","childs":[]}]},{"path":"\/welfare-management","code":"bgm-ehr.welfare-manage","name":"\u798f\u5229\u7ba1\u7406","pid":2820,"id":3223,"type":"modu","childs":[{"path":"\/list","code":"bgm-ehr.welfare-manage.list","name":"\u798f\u5229\u5217\u8868","pid":3223,"id":3224,"type":"modu","childs":[]},{"path":"\/type","code":"bgm-ehr.welfare-manage.type","name":"\u798f\u5229\u7c7b\u578b","pid":3223,"id":3225,"type":"modu","childs":[]},{"path":"\/birthday","code":"bgm-ehr.welfare-manage.birthday","name":"\u751f\u65e5\u4f1a","pid":3223,"id":3315,"type":"modu","childs":[]}]},{"path":"\/data","code":"bgm-ehr.data","name":"\u6570\u636e\u7edf\u8ba1","pid":2820,"id":3302,"type":"modu","childs":[{"path":"\/month","code":"bgm-ehr.data.month","name":"\u6708\u5ea6\u4eba\u5458\u5206\u6790","pid":3302,"id":3303,"type":"modu","childs":[]},{"path":"\/department","code":"bgm-ehr.data.department","name":"\u90e8\u95e8\u4eba\u5458\u5206\u6790","pid":3302,"id":3304,"type":"modu","childs":[]},{"path":"\/rank","code":"bgm-ehr.data.rank","name":"\u804c\u7ea7\u7ed3\u6784\u5206\u6790","pid":3302,"id":3305,"type":"modu","childs":[{"path":"\/month","code":"bgm-ehr.data.rank.month","name":"\u5404\u6708\u804c\u7ea7\u7ed3\u6784\u5206\u6790","pid":3305,"id":3306,"type":"modu","childs":[]},{"path":"\/compare","code":"bgm-ehr.data.rank.compare","name":"\u804c\u7ea7\u6bd4\u5206\u6790","pid":3305,"id":3307,"type":"modu","childs":[]}]},{"path":"\/sex","code":"bgm-ehr.data.sex","name":"\u6027\u522b\u7ed3\u6784\u5206\u6790","pid":3302,"id":3308,"type":"modu","childs":[]},{"path":"\/education","code":"bgm-ehr.data.education","name":"\u6559\u80b2\u80cc\u666f\u5206\u6790","pid":3302,"id":3309,"type":"modu","childs":[]},{"path":"\/age","code":"bgm-ehr.data.age","name":"\u5e74\u9f84\u7ed3\u6784\u5206\u6790","pid":3302,"id":3310,"type":"modu","childs":[]},{"path":"\/siling","code":"bgm-ehr.data.siling","name":"\u53f8\u9f84\u7ed3\u6784\u5206\u6790","pid":3302,"id":3311,"type":"modu","childs":[]},{"path":"\/gongling","code":"bgm-ehr.data.gongling","name":"\u5de5\u9f84\u7ed3\u6784\u5206\u6790","pid":3302,"id":3312,"type":"modu","childs":[]},{"path":"\/quit","code":"bgm-ehr.data.quit","name":"\u79bb\u804c\u539f\u56e0\u5206\u6790","pid":3302,"id":3313,"type":"modu","childs":[]},{"path":"\/hc","code":"bgm-ehr.data.hc","name":"HC\u6c47\u603b\u5206\u6790","pid":3302,"id":3803,"type":"modu","childs":[]}]},{"path":"\/entry_manage","code":"bgm-ehr.entry_manage","name":"\u5165\u804c\u7ba1\u7406","pid":2820,"id":3798,"type":"modu","childs":[{"path":"\/xingzheng","code":"bgm-ehr.entry_manage.xingzheng","name":"\u884c\u653f","pid":3798,"id":3799,"type":"modu","childs":[]},{"path":"\/it","code":"bgm-ehr.entry_manage.it","name":"\u4fe1\u7ba1","pid":3798,"id":3800,"type":"modu","childs":[]},{"path":"\/head","code":"bgm-ehr.entry_manage.head","name":"\u90e8\u95e8\u4e3b\u7ba1","pid":3798,"id":3801,"type":"modu","childs":[]}]},{"path":"\/assessment","code":"bgm-ehr.assessment","name":"\u6a21\u677f\u914d\u7f6e","pid":2820,"id":13164,"type":"modu","childs":[{"path":"\/indicators","code":"bgm-ehr.assessment.indicators","name":"\u6307\u6807\u914d\u7f6e\u8868","pid":13164,"id":13166,"type":"modu","childs":[]},{"path":"\/university","code":"bgm-ehr.assessment.university","name":"\u5927\u5b66\u8003\u6838\u914d\u7f6e","pid":13164,"id":13170,"type":"modu","childs":[]},{"path":"\/weight","code":"bgm-ehr.assessment.weight","name":"\u6743\u91cd\u914d\u7f6e","pid":13164,"id":13174,"type":"modu","childs":[]},{"path":"\/values","code":"bgm-ehr.assessment.values","name":"\u4ef7\u503c\u89c2\u8003\u6838\u914d\u7f6e","pid":13164,"id":13178,"type":"modu","childs":[]},{"path":"\/assessment_department_indicators","code":"bgm-ehr.assessment.assessment_department_indicators","name":"\u6a21\u677f\u914d\u7f6e\u8868","pid":13164,"id":13183,"type":"modu","childs":[]},{"path":"\/remind","code":"bgm-ehr.assessment.remind","name":"\u90ae\u4ef6\u63d0\u9192\u914d\u7f6e","pid":13164,"id":13188,"type":"modu","childs":[]}]}],"project":{"path":"\/","code":"bgm-ehr","icon":"43.png","name":"E-HR","pid":0,"id":2820,"type":"app"}},"success":true,"total":195}222{"error_code":0,"error_msg":"","data":{"project":{"path":"\/","code":"bgm-ehr","icon":"43.png","name":"E-HR","pid":0,"id":2820,"type":"app"},"permissions":["bgm-ehr.performance-manage","bgm-ehr.system-manage.acl-manage","bgm-ehr.employee-manage","bgm-ehr.department-manage","bgm-ehr.employee-manage.staff.view","bgm-ehr.employee-manage.recruit.add","bgm-ehr.employee-manage.staff.edit","bgm-ehr.department-manage.add","bgm-ehr.department-manage.edit","bgm-ehr.archive-manage","bgm-ehr.vacation_stats","bgm-ehr.vacation_stats.view","bgm-ehr.vacation_stats.edit","bgm-ehr.salary.import","bgm-ehr.salary","bgm-ehr.archive-manage.archive-list","bgm-ehr.archive-manage.contract-template","bgm-ehr.archive-manage.archive-list.view","bgm-ehr.archive-manage.archive-list.edit","bgm-ehr.archive-manage.contract-template.edit","bgm-ehr.employee-manage.recruit.delete","bgm-ehr.employee-manage.staff.export","bgm-ehr.employee-manage.staff.export-select","bgm-ehr.department-manage.delete","bgm-ehr.department-manage.sort","bgm-ehr.archive-manage.archive-list.export-all","bgm-ehr.archive-manage.archive-list.export-select","bgm-ehr.archive-manage.contract-template.export","bgm-ehr.archive-manage.contract-template.delete","bgm-ehr.vacation_stats.import","bgm-ehr.vacation_stats.update","bgm-ehr.vacation_stats.export-all","bgm-ehr.vacation_stats.export-select","bgm-ehr.system-manage","bgm-ehr.standby-manage.list","bgm-ehr.standby-manage.set","bgm-ehr.standby-manage.set.add","bgm-ehr.standby-manage.set.edit","bgm-ehr.standby-manage.set.delete","bgm-ehr.department-manage.view","bgm-ehr.salary.view","bgm-ehr.employee-manage.staff.adjustment-department","bgm-ehr.employee-manage.staff.trajectory","bgm-ehr.standby-manage","bgm-ehr.welfare-manage.list","bgm-ehr.welfare-manage.type","bgm-ehr.welfare-manage.list.add","bgm-ehr.welfare-manage.list.edit","bgm-ehr.welfare-manage.list.delete","bgm-ehr.welfare-manage.list.export-all","bgm-ehr.welfare-manage.list.export-select","bgm-ehr.welfare-manage.type.add","bgm-ehr.welfare-manage.type.edit","bgm-ehr.welfare-manage.type.delete","bgm-ehr.welfare-manage","bgm-ehr.data.month","bgm-ehr.data.department","bgm-ehr.data.rank","bgm-ehr.data.rank.month","bgm-ehr.data.rank.compare","bgm-ehr.data.sex","bgm-ehr.data.education","bgm-ehr.data.age","bgm-ehr.data.siling","bgm-ehr.data.gongling","bgm-ehr.data.quit","bgm-ehr.welfare-manage.birthday","bgm-ehr.welfare-manage.birthday.view","bgm-ehr.welfare-manage.birthday.export-all","bgm-ehr.welfare-manage.birthday.export-select","bgm-ehr.system-manage.log","bgm-ehr.data","bgm-ehr.performance-manage.mine","bgm-ehr.performance-manage.all","bgm-ehr.performance-manage.transfer","bgm-ehr.performance-manage.rank-list","bgm-ehr.performance-manage.rank-manage","bgm-ehr.performance-manage.mine.view","bgm-ehr.performance-manage.mine.edit","bgm-ehr.performance-manage.mine.delete","bgm-ehr.performance-manage.mine.confirm","bgm-ehr.performance-manage.mine.export","bgm-ehr.performance-manage.transfer.daipingfen.view","bgm-ehr.performance-manage.rank-list.view","bgm-ehr.performance-manage.rank-list.edit","bgm-ehr.performance-manage.rank-list.change","bgm-ehr.performance-manage.rank-manage.set","bgm-ehr.performance-manage.rank-manage.number","bgm-ehr.performance-manage.rank-manage.chart","bgm-ehr.performance-manage.rank-manage.set.view","bgm-ehr.performance-manage.rank-manage.set.edit","bgm-ehr.performance-manage.rank-manage.set.confirm","bgm-ehr.performance-manage.rank-manage.set.apply","bgm-ehr.performance-manage.rank-manage.number.view","bgm-ehr.performance-manage.rank-manage.number.edit","bgm-ehr.performance-manage.rank-manage.number.export","bgm-ehr.performance-manage.rank-list.export-all","bgm-ehr.performance-manage.rank-list.export-select","bgm-ehr.performance-manage.rank-list.confirm-select","bgm-ehr.performance-manage.all.jindu.export-all","bgm-ehr.performance-manage.all.jindu.export-select","bgm-ehr.performance-manage.mine.add","bgm-ehr.performance-manage.rank-sum","bgm-ehr.performance-manage.rank-sum.edit","bgm-ehr.performance-manage.rank-sum.change","bgm-ehr.performance-manage.rank-sum.view","bgm-ehr.performance-manage.rank-sum.publish","bgm-ehr.performance-manage.rank-sum.export-select","bgm-ehr.performance-manage.rank-sum.export-all","bgm-ehr.employee-manage.recruit","bgm-ehr.employee-manage.entry","bgm-ehr.employee-manage.staff","bgm-ehr.employee-manage.quit","bgm-ehr.employee-manage.give_up","bgm-ehr.employee-manage.recruit.view","bgm-ehr.employee-manage.recruit.edit","bgm-ehr.employee-manage.recruit.recruited","bgm-ehr.employee-manage.recruit.export-all","bgm-ehr.employee-manage.recruit.export-select","bgm-ehr.employee-manage.entry.view","bgm-ehr.employee-manage.entry.edit","bgm-ehr.employee-manage.entry.go","bgm-ehr.employee-manage.entry.giveup","bgm-ehr.employee-manage.entry.export-all","bgm-ehr.employee-manage.entry.export-select","bgm-ehr.employee-manage.quit.view","bgm-ehr.employee-manage.quit.export-all","bgm-ehr.employee-manage.quit.export-select","bgm-ehr.employee-manage.give_up.view","bgm-ehr.employee-manage.give_up.export-all","bgm-ehr.employee-manage.give_up.export-select","bgm-ehr.entry_manage","bgm-ehr.entry_manage.xingzheng","bgm-ehr.entry_manage.it","bgm-ehr.entry_manage.head","bgm-ehr.entry_manage.head.view","bgm-ehr.data.hc","bgm-ehr.system-manage.set","bgm-ehr.system-manage.set.view","bgm-ehr.system-manage.set.edit","bgm-ehr.employee-manage.quit.edit","bgm-ehr.performance-manage.all.jindu","bgm-ehr.performance-manage.all.liushui","bgm-ehr.performance-manage.all.jindu.chakan","bgm-ehr.performance-manage.all.jindu.biangeng","bgm-ehr.performance-manage.all.liushui.export-all","bgm-ehr.performance-manage.all.liushui.export-select","bgm-ehr.performance-manage.all.liushui.bohui-select","bgm-ehr.performance-manage.all.liushui.delect-select","bgm-ehr.performance-manage.transfer.export-all","bgm-ehr.performance-manage.transfer.export-select","bgm-ehr.performance-manage.transfer.daipingfen.pingfen","bgm-ehr.performance-manage.transfer.daipingfen.biangeng","bgm-ehr.performance-manage.transfer.daipingfen","bgm-ehr.performance-manage.transfer.daiqueren","bgm-ehr.performance-manage.transfer.daiqueren.bianji","bgm-ehr.performance-manage.transfer.daiqueren.chakan","bgm-ehr.performance-manage.transfer.daiqueren.biangeng","bgm-ehr.performance-manage.transfer.daiqueren.tongguo","bgm-ehr.performance-manage.transfer.daiqueren.bohui","bgm-ehr.performance-manage.rank-list.queren-all","bgm-ehr.performance-manage.rank-sum.queren-select","bgm-ehr.performance-manage.rank-sum.queren-all","bgm-ehr.performance-manage.admin","bgm-ehr.performance-manage.admin.bohui","bgm-ehr.performance-manage.admin.delect","bgm-ehr.performance-manage.admin.chakan","bgm-ehr.assessment","bgm-ehr.assessment.indicators","bgm-ehr.assessment.indicators.list","bgm-ehr.assessment.indicators.save","bgm-ehr.assessment.indicators.delete","bgm-ehr.assessment.university","bgm-ehr.assessment.university.list","bgm-ehr.assessment.university.save","bgm-ehr.assessment.university.delete","bgm-ehr.assessment.weight","bgm-ehr.assessment.weight.list","bgm-ehr.assessment.weight.save","bgm-ehr.assessment.weight.delete","bgm-ehr.assessment.values","bgm-ehr.assessment.values.list","bgm-ehr.assessment.values.save","bgm-ehr.assessment.values.delete","bgm-ehr.assessment.assessment_department_indicators","bgm-ehr.assessment.assessment_department_indicators.getList","bgm-ehr.assessment.assessment_department_indicators.addEdit","bgm-ehr.assessment.assessment_department_indicators.del","bgm-ehr.assessment.assessment_department_indicators.getDetail","bgm-ehr.assessment.remind","bgm-ehr.assessment.remind.list","bgm-ehr.assessment.remind.edit","bgm-ehr.assessment.remind.detail","bgm-ehr.assessment.remind.delete"],"left_menu":[{"path":"\/performance-manage","code":"bgm-ehr.performance-manage","name":"\u7ee9\u6548\u7ba1\u7406123","pid":2820,"id":3450,"type":"modu","childs":[{"path":"\/mine","code":"bgm-ehr.performance-manage.mine","name":"\u6211\u7684\u7ee9\u6548","pid":3450,"id":3451,"type":"modu","childs":[]},{"path":"\/all","code":"bgm-ehr.performance-manage.all","name":"\u5168\u90e8\u7ee9\u6548","pid":3450,"id":3452,"type":"modu","childs":[{"path":"\/","code":"bgm-ehr.performance-manage.all.jindu","name":"\u8003\u6838\u8fdb\u5ea6","pid":3452,"id":4021,"type":"modu","childs":[]},{"path":"\/","code":"bgm-ehr.performance-manage.all.liushui","name":"\u7ee9\u6548\u6d41\u6c34","pid":3452,"id":4022,"type":"modu","childs":[]}]},{"path":"\/transfer","code":"bgm-ehr.performance-manage.transfer","name":"\u7ee9\u6548\u6d41\u8f6c","pid":3450,"id":3453,"type":"modu","childs":[{"path":"\/","code":"bgm-ehr.performance-manage.transfer.daipingfen","name":"\u5f85\u8bc4\u5206","pid":3453,"id":4033,"type":"modu","childs":[]},{"path":"\/","code":"bgm-ehr.performance-manage.transfer.daiqueren","name":"\u5f85\u786e\u8ba4","pid":3453,"id":4034,"type":"modu","childs":[]}]},{"path":"\/rank-list","code":"bgm-ehr.performance-manage.rank-list","name":"\u7b49\u7ea7\u5217\u8868","pid":3450,"id":3454,"type":"modu","childs":[]},{"path":"\/rank-manage","code":"bgm-ehr.performance-manage.rank-manage","name":"\u7b49\u7ea7\u7ba1\u7406","pid":3450,"id":3455,"type":"modu","childs":[{"path":"\/set","code":"bgm-ehr.performance-manage.rank-manage.set","name":"\u7b49\u7ea7\u8bbe\u5b9a","pid":3455,"id":3467,"type":"modu","childs":[]},{"path":"\/number","code":"bgm-ehr.performance-manage.rank-manage.number","name":"\u7b49\u7ea7\u6570\u91cf","pid":3455,"id":3468,"type":"modu","childs":[]},{"path":"\/chart","code":"bgm-ehr.performance-manage.rank-manage.chart","name":"\u5206\u5e03\u5bf9\u6bd4","pid":3455,"id":3469,"type":"modu","childs":[]}]},{"path":"\/rank-sum","code":"bgm-ehr.performance-manage.rank-sum","name":"\u7b49\u7ea7\u6c47\u603b","pid":3450,"id":3656,"type":"modu","childs":[{"path":"\/","code":"bgm-ehr.performance-manage.rank-sum.export-select","name":"\u5bfc\u51fa\u52fe\u9009","pid":3656,"id":3661,"type":"modu","childs":[]}]},{"path":"\/","code":"bgm-ehr.performance-manage.admin","name":"\u7ee9\u6548\u8d85\u7ba1","pid":3450,"id":4043,"type":"modu","childs":[]}]},{"path":"\/employee-manage","code":"bgm-ehr.employee-manage","name":"\u5458\u5de5\u7ba1\u7406","pid":2820,"id":2824,"type":"modu","childs":[{"path":"\/recruit","code":"bgm-ehr.employee-manage.recruit","name":"\u5f85\u62db\u8058","pid":2824,"id":3776,"type":"modu","childs":[]},{"path":"\/entry","code":"bgm-ehr.employee-manage.entry","name":"\u5f85\u5165\u804c","pid":2824,"id":3777,"type":"modu","childs":[]},{"path":"\/staff","code":"bgm-ehr.employee-manage.staff","name":"\u5728\u804c","pid":2824,"id":3778,"type":"modu","childs":[]},{"path":"\/quit","code":"bgm-ehr.employee-manage.quit","name":"\u79bb\u804c","pid":2824,"id":3779,"type":"modu","childs":[]},{"path":"\/give_up","code":"bgm-ehr.employee-manage.give_up","name":"\u653e\u5f03\u5165\u804c","pid":2824,"id":3780,"type":"modu","childs":[]}]},{"path":"\/department-manage","code":"bgm-ehr.department-manage","name":"\u90e8\u95e8\u7ba1\u7406","pid":2820,"id":2823,"type":"modu","childs":[]},{"path":"\/archive-manage","code":"bgm-ehr.archive-manage","name":"\u6863\u6848\u7ba1\u7406","pid":2820,"id":2961,"type":"modu","childs":[{"path":"\/archive-list","code":"bgm-ehr.archive-manage.archive-list","name":"\u6863\u6848\u5217\u8868","pid":2961,"id":2962,"type":"modu","childs":[]},{"path":"\/contract-template","code":"bgm-ehr.archive-manage.contract-template","name":"\u5408\u540c\u6a21\u677f","pid":2961,"id":2963,"type":"modu","childs":[]}]},{"path":"\/vacation-stats","code":"bgm-ehr.vacation_stats","name":"\u5047\u671f\u7ba1\u7406","pid":2820,"id":2904,"type":"modu","childs":[]},{"path":"\/salary","code":"bgm-ehr.salary","name":"\u85aa\u916c\u7ba1\u7406","pid":2820,"id":2903,"type":"modu","childs":[]},{"path":"\/system-manage","code":"bgm-ehr.system-manage","name":"\u7cfb\u7edf\u7ba1\u7406","pid":2820,"id":2821,"type":"modu","childs":[{"path":"\/system_manage\/acl_manage","code":"bgm-ehr.system-manage.acl-manage","name":"\u6743\u9650\u7ba1\u7406","pid":2821,"id":2822,"type":"modu","childs":[]},{"path":"\/log","code":"bgm-ehr.system-manage.log","name":"\u65e5\u5fd7\u7ba1\u7406","pid":2821,"id":3347,"type":"modu","childs":[]},{"path":"\/set","code":"bgm-ehr.system-manage.set","name":"\u5b57\u6bb5\u8bbe\u7f6e","pid":2821,"id":3804,"type":"modu","childs":[]}]},{"path":"\/standby-manage","code":"bgm-ehr.standby-manage","name":"\u5f85\u529e\u7ba1\u7406","pid":2820,"id":3149,"type":"modu","childs":[{"path":"\/list","code":"bgm-ehr.standby-manage.list","name":"\u5f85\u529e\u4e8b\u9879","pid":3149,"id":3150,"type":"modu","childs":[]},{"path":"\/set","code":"bgm-ehr.standby-manage.set","name":"\u5f85\u529e\u8bbe\u7f6e","pid":3149,"id":3151,"type":"modu","childs":[]}]},{"path":"\/welfare-management","code":"bgm-ehr.welfare-manage","name":"\u798f\u5229\u7ba1\u7406","pid":2820,"id":3223,"type":"modu","childs":[{"path":"\/list","code":"bgm-ehr.welfare-manage.list","name":"\u798f\u5229\u5217\u8868","pid":3223,"id":3224,"type":"modu","childs":[]},{"path":"\/type","code":"bgm-ehr.welfare-manage.type","name":"\u798f\u5229\u7c7b\u578b","pid":3223,"id":3225,"type":"modu","childs":[]},{"path":"\/birthday","code":"bgm-ehr.welfare-manage.birthday","name":"\u751f\u65e5\u4f1a","pid":3223,"id":3315,"type":"modu","childs":[]}]},{"path":"\/data","code":"bgm-ehr.data","name":"\u6570\u636e\u7edf\u8ba1","pid":2820,"id":3302,"type":"modu","childs":[{"path":"\/month","code":"bgm-ehr.data.month","name":"\u6708\u5ea6\u4eba\u5458\u5206\u6790","pid":3302,"id":3303,"type":"modu","childs":[]},{"path":"\/department","code":"bgm-ehr.data.department","name":"\u90e8\u95e8\u4eba\u5458\u5206\u6790","pid":3302,"id":3304,"type":"modu","childs":[]},{"path":"\/rank","code":"bgm-ehr.data.rank","name":"\u804c\u7ea7\u7ed3\u6784\u5206\u6790","pid":3302,"id":3305,"type":"modu","childs":[{"path":"\/month","code":"bgm-ehr.data.rank.month","name":"\u5404\u6708\u804c\u7ea7\u7ed3\u6784\u5206\u6790","pid":3305,"id":3306,"type":"modu","childs":[]},{"path":"\/compare","code":"bgm-ehr.data.rank.compare","name":"\u804c\u7ea7\u6bd4\u5206\u6790","pid":3305,"id":3307,"type":"modu","childs":[]}]},{"path":"\/sex","code":"bgm-ehr.data.sex","name":"\u6027\u522b\u7ed3\u6784\u5206\u6790","pid":3302,"id":3308,"type":"modu","childs":[]},{"path":"\/education","code":"bgm-ehr.data.education","name":"\u6559\u80b2\u80cc\u666f\u5206\u6790","pid":3302,"id":3309,"type":"modu","childs":[]},{"path":"\/age","code":"bgm-ehr.data.age","name":"\u5e74\u9f84\u7ed3\u6784\u5206\u6790","pid":3302,"id":3310,"type":"modu","childs":[]},{"path":"\/siling","code":"bgm-ehr.data.siling","name":"\u53f8\u9f84\u7ed3\u6784\u5206\u6790","pid":3302,"id":3311,"type":"modu","childs":[]},{"path":"\/gongling","code":"bgm-ehr.data.gongling","name":"\u5de5\u9f84\u7ed3\u6784\u5206\u6790","pid":3302,"id":3312,"type":"modu","childs":[]},{"path":"\/quit","code":"bgm-ehr.data.quit","name":"\u79bb\u804c\u539f\u56e0\u5206\u6790","pid":3302,"id":3313,"type":"modu","childs":[]},{"path":"\/hc","code":"bgm-ehr.data.hc","name":"HC\u6c47\u603b\u5206\u6790","pid":3302,"id":3803,"type":"modu","childs":[]}]},{"path":"\/entry_manage","code":"bgm-ehr.entry_manage","name":"\u5165\u804c\u7ba1\u7406","pid":2820,"id":3798,"type":"modu","childs":[{"path":"\/xingzheng","code":"bgm-ehr.entry_manage.xingzheng","name":"\u884c\u653f","pid":3798,"id":3799,"type":"modu","childs":[]},{"path":"\/it","code":"bgm-ehr.entry_manage.it","name":"\u4fe1\u7ba1","pid":3798,"id":3800,"type":"modu","childs":[]},{"path":"\/head","code":"bgm-ehr.entry_manage.head","name":"\u90e8\u95e8\u4e3b\u7ba1","pid":3798,"id":3801,"type":"modu","childs":[]}]},{"path":"\/assessment","code":"bgm-ehr.assessment","name":"\u6a21\u677f\u914d\u7f6e","pid":2820,"id":13164,"type":"modu","childs":[{"path":"\/indicators","code":"bgm-ehr.assessment.indicators","name":"\u6307\u6807\u914d\u7f6e\u8868","pid":13164,"id":13166,"type":"modu","childs":[]},{"path":"\/university","code":"bgm-ehr.assessment.university","name":"\u5927\u5b66\u8003\u6838\u914d\u7f6e","pid":13164,"id":13170,"type":"modu","childs":[]},{"path":"\/weight","code":"bgm-ehr.assessment.weight","name":"\u6743\u91cd\u914d\u7f6e","pid":13164,"id":13174,"type":"modu","childs":[]},{"path":"\/values","code":"bgm-ehr.assessment.values","name":"\u4ef7\u503c\u89c2\u8003\u6838\u914d\u7f6e","pid":13164,"id":13178,"type":"modu","childs":[]},{"path":"\/assessment_department_indicators","code":"bgm-ehr.assessment.assessment_department_indicators","name":"\u6a21\u677f\u914d\u7f6e\u8868","pid":13164,"id":13183,"type":"modu","childs":[]},{"path":"\/remind","code":"bgm-ehr.assessment.remind","name":"\u90ae\u4ef6\u63d0\u9192\u914d\u7f6e","pid":13164,"id":13188,"type":"modu","childs":[]}]}],"user":{"roleCodes":"role_admin, role-ehr-hrbp, role-all, role-ehr-sup, role-res-admin","userName":"miaoyulu","userId":12170,"bindId":0,"loginCount":0,"userCode":"miaoyulu","realName":"\u82d7\u96e8\u9732","roleIds":"2, 78, 135, 136, 284","empCode":"601683","lastUpdate":1617087826000,"roleNames":"\u8d85\u7ea7\u7ba1\u7406\u5458, HRBP, \u5168\u5458, EHR\u4e3b\u7ba1, \u8d85\u7ea7\u7ba1\u7406\u5458","email":"miaoyulu@smzdm.com","status":"1"}}}',true);
        if(!$userPower){
            $result['error_code']=4;
            $result['error_mas']='获取用户权限错误';
            goto RESULT;
        }

        $result['data'] = $userPower['result'];
        $result['data']['user'] = $userinfo['result'];

        RESULT:
        return $result;
    }

    /**
     * User  :zhangsiming · Date: 2016/11/17 11:47
     * Info  :获取用户信息与权限
     * Param :string $appCode项目的唯一标识
     * Param :string $treelike默认为true返回格式化的数据，false返回未格式化的数据
     * Return:array
     */
    function getUserRbac($appCode,$treelike){
        // CAS打开，走新的获取逻辑
        if (Config::$cas['open']) {
            // liangdong@smzdm.com 2018-04-23
            $casData = $this->cas->getUserInfo(false);
            if(empty($casData)) {
                $result['error_code'] = '1';
                $result['error_msg'] = '无法获取COOKIE';
                goto RESULT;
            }
            $userId = $casData['attributes']['uid'];
            return $this->getUserRbacWithId($appCode, $treelike, $userId);
        }
        // 保持老代码兼容
        $result=['error_code'=>0,'error_msg'=>'','data'=>''];
        // zhangsiming · 2016/11/1 11:34 ·  获取java生成的cookie
        $user_cookie=isset($_COOKIE['SSO_SMZDM'])?$_COOKIE["SSO_SMZDM"]:'';
        if(empty($user_cookie)){
            $result['error_code']='1';
            $result['error_msg']='无法获取COOKIE';
            goto RESULT;
        }
        if(empty($appCode)){
            $result['error_code']='2';
            $result['error_msg']='项目ID不得为空';
            goto RESULT;
        }
        // zhangsiming · 2016/11/17 11:50· 获取用户信息
        $userinfo=$this->user_rbac_api->get_user_info($user_cookie);
        if(!$userinfo){
            $result['error_code']='3';
            $result['error_msg']='获取用户信息失败';
            goto RESULT;
        }
        $userId=$userinfo['result']['user']['userId'];
        //根据用户信息获取用户权限
        $userPower=$this->user_rbac_api->get_user_power($userId,$appCode,$treelike);
        if(!$userPower){
            $result['error_code']=4;
            $result['error_mas']='获取用户权限错误';
            goto RESULT;
        }
        $result['data']=$userPower['result'];
        $result['data']['user']=$userinfo['result']['user'];
        RESULT:
        return $result;
    }


    /**
     * User  :zhangsiming · Date: 2016/11/18 17:49
     * Info  :通过用户id获取用户信息
     * Param :string $userId用户id
     * Return:array
     */
    public function getUser($userId){
        $result=['error_code'=>0,'error_msg'=>'','data'=>''];
        if(empty($userId)){
            $result['error_code']=1;
            $result['error_msg']='userId不得为空';
            goto RELULT;
        }
        $user=$this->user_rbac_api->get_user($userId);
        if(!$user || $user['success']==false){
            $result['error_code']=2;
            $result['error_msg']='获取用户信息失败';
            goto RELULT;
        }
        $result['data']=$user['result'];
        RELULT:
        return $result;
    }

    /**
     * @param $userId
     * @return array
     */
    public function batchGetUserInfo($userId) {
        $result=['error_code'=>0,'error_msg'=>'','data'=>''];
        if(empty($userId)){
            $result['error_code']=1;
            $result['error_msg']='userId不得为空';
            goto RELULT;
        }
        $user=$this->user_rbac_api->batchGetUserInfo($userId);
        if(!$user || $user['success']==false){
            $result['error_code']=2;
            $result['error_msg']='获取用户信息失败';
            goto RELULT;
        }
        $result['data']=$user['result'];
        RELULT:
        return $result;
    }

    /**
     * 获得某用户多个项目的权限
     *      接口文档： http://twiki:8081/twiki/Main/Api_getResByMultiApps
     *
     * @param   array   $args       条件参数
     * @return  array|null
     * @author  Dacheng Chen
     * @time    2016-12-8
     */
    public function get_auth_permission_data($args = []){
        $default_args = [
            'userId' => 0,
            'appCode' => [],
        ];
        $args = array_merge($default_args, $args);
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];

        $args['userId'] = intval($args['userId']);
        if($args['userId'] <= 0){
            $r['error_code'] = 1;
            $r['error_msg'] = '必要参数获取失败';
            goto ARCHOR_RESULT;
        }

        $result = $this->user_rbac_api->get_auth_permission_data($args);
        if(is_null($result)){
            $r['error_code'] = -1;
            $r['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        }
        if(!isset($result['success']) || empty($result['success'])){
            $r['error_code'] = 1;
            $r['error_msg'] = '获取数据失败';
            goto ARCHOR_RESULT;
        }
        if(empty($result['result'])){
            goto ARCHOR_RESULT;
        }
        $r['data'] = $result['result'];

        ARCHOR_RESULT:
        return $r;
    }

    /**
     * 根据cookie获取用户信息
     *
     * @return array
     * @author Dongdong Wang
     * @date 2016-12-08
     */
    public function get_userinfo_by_cookie() {
        $result=['error_code'=>0,'error_msg'=>'','data'=>[]];
        // zhangsiming · 2016/11/1 11:34 ·  获取java生成的cookie
        $user_cookie = !empty($_COOKIE['SSO_SMZDM'])?$_COOKIE["SSO_SMZDM"]:'';
        if(empty($user_cookie)){
            $result['error_code']=1;
            $result['error_msg']='无法获取COOKIE';
            goto RESULT;
        }
        // zhangsiming · 2016/11/17 11:50· 获取用户信息
        $userinfo=$this->user_rbac_api->get_user_info($user_cookie);
        if(!$userinfo || empty($userinfo['result']['user'])){
            $result['error_code']=-1;
            $result['error_msg']='网络错误';
            goto RESULT;
        }

        $result['data'] = $userinfo['result']['user'];
        RESULT:
        return $result;
    }

    /**
     * 根据角色查询所有用户集合
     *
     * @param  string   $role_code  SSO系统用户角色编码
     * @return array|null
     * @author  Dacheng Chen
     * @time    2016-12-18
     */
    public function get_user_by_role($role_code){
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];
        $role_code = trim($role_code);
        if(empty($role_code)){
            $result['error_code'] = ERROR_PARAMS;
            $result['error_msg'] = '角色code不能为空';
            goto ARCHOR_RESULT;
        }

        $res = $this->user_rbac_api->get_user_by_role(['roleCode' => $role_code]);
        if(empty($res)){ #含null
            $result['error_code'] = ERROR_THIRD;
            $result['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        }
        if(empty($res['success'])){
            $r['error_code'] = 1;
            $r['error_msg'] = !empty($res['error']) ? trim($res['error']) : '根据角色code获得用户信息失败';
            goto ARCHOR_RESULT;
        }
        if(empty($res['result']) || empty($res['total'])){
            goto ARCHOR_RESULT;
        }

        $r['data']['total'] = $res['total'];
        foreach($res['result'] as $val){
            $_temp = [
                'sso_userid' => $val['userId'], #sso系统用户ID
                'sso_username' => $val['userCode'], #sso系统用户账号
                'sso_nickname' => $val['realName'], #sso系统用户真实姓名
            ];
            $r['data']['rows'][] = $_temp;
        }

        ARCHOR_RESULT:
        return $r;
    }

    /**
     * 根据系统标识批量获取用户信息
     *      bgm-sos，仅返回所属系统角色
     *
     * @param  array    $args       参数
     * @return array
     * @author  Dacheng Chen
     * @time    2016-12-18
     */
    public function get_user_list_by_rescode($args = []){
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];
        $default_args = [
            'user_ids' => '',
            'app_code' => 'zindex', #系统标识，约定会写死使用'zindex'
        ];
        $args = array_merge($default_args, $args);

        #筛选数据
        $params = [
            'userIds' => $args['user_ids'],
            'appCode' => $args['app_code'],
        ];
        $res = $this->user_rbac_api->get_user_list_by_rescode($params);
        if(empty($res)){ #含null
            $result['error_code'] = ERROR_THIRD;
            $result['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        }
        if(empty($res['success'])){
            $r['error_code'] = 1;
            $r['error_msg'] = !empty($res['error']) ? trim($res['error']) : '根据系统标识批量获取用户信息失败';
            goto ARCHOR_RESULT;
        }
        if(empty($res['result']) || empty($res['total'])){
            goto ARCHOR_RESULT;
        }

        $r['data']['total'] = $res['total'];
        foreach($res['result'] as $val){
            $_temp = [
                'sso_userid' => $val['userId'], #sso系统用户ID
                'sso_username' => $val['userCode'], #sso系统用户账号
                'sso_nickname' => $val['realName'], #sso系统用户真实姓名
                'role_names' => !empty($val['roleNames']) ? $val['roleNames'] : '', #SSO用户角色名称，英文逗号分隔
            ];
            $r['data']['rows'][] = $_temp;
        }

        ARCHOR_RESULT:
        return $r;
    }
}