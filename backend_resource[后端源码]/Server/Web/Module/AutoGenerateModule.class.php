<?php

/**
 * @name eolinker ams open source，eolinker开源版本
 * @link https://www.eolinker.com/
 * @package eolinker
 * @author www.eolinker.com 广州银云信息科技有限公司 ©2015-2018
 * eoLinker是目前全球领先、国内最大的在线API接口管理平台，提供自动生成API文档、API自动化测试、Mock测试、团队协作等功能，旨在解决由于前后端分离导致的开发效率低下问题。
 * 如在使用的过程中有任何问题，欢迎加入用户讨论群进行反馈，我们将会以最快的速度，最好的服务态度为您解决问题。
 *
 * eoLinker AMS开源版的开源协议遵循Apache License 2.0，如需获取最新的eolinker开源版以及相关资讯，请访问:https://www.eolinker.com/#/os/download
 *
 * 官方网站：https://www.eolinker.com/
 * 官方博客以及社区：http://blog.eolinker.com/
 * 使用教程以及帮助：http://help.eolinker.com/
 * 商务合作邮箱：market@eolinker.com
 * 用户讨论QQ群：284421832
 */
class AutoGenerateModule
{
    /**
     * 导入接口
     * @param $data
     * @param $project_id
     * @param $user_id
     * @return bool
     */
    public function importApi(&$data, &$project_id, &$user_id)
    {
        $dao = new AutoGenerateDao();
        $result = $dao->importApi($data, $project_id);
        if ($result) {
            $log_dao = new ProjectLogDao();
            $log_dao->addOperationLog($project_id, $user_id, ProjectLogDao::$OP_TARGET_PROJECT, $project_id, ProjectLogDao::$OP_TYPE_UPDATE, '通过自动生成文档功能更新接口文档', date('Y-m-d H:i:s', time()));
            return $result;
        } else {
            return FALSE;
        }
    }

    /**
     * 检查项目权限
     * @param $user_name
     * @param $user_password
     * @param $project_id
     * @return bool|array
     */
    public function checkProjectPermission(&$user_name, &$user_password, &$project_id)
    {
        $dao = new GuestDao;
        $user_info = $dao->getLoginInfo($user_name);
        if (md5($user_password) == $user_info['userPassword']) {
            $project_dao = new ProjectDao();
            if ($project_dao->checkProjectPermission($project_id, $user_info['userID'])) {
                $auth_dao = new AuthorizationDao();
                $result = $auth_dao->getProjectUserType($user_info['userID'], $project_id);
                if ($result === FALSE) {
                    return FALSE;
                } elseif ($result > 2) {
                    return FALSE;
                } else {
                    return $user_info;
                }
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
}