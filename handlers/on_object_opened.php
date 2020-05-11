<?php

/**
 * @Author: Anton Baranov
 * @Date:   2020-05-10 22:11:30
 * @Last Modified by:   Anton Baranov
 * @Last Modified time: 2020-05-11 13:31:51
 */

function rocketchat_handle_on_object_opened($object) {
    if (defined('ROCKETCHAT_TOKEN') and defined('ROCKETCHAT_URL') and defined('ROCKETCHAT_USERID')) {

        $rocketchat = new RocketChat(ROCKETCHAT_URL, ROCKETCHAT_TOKEN, ROCKETCHAT_USERID);

        if ($object instanceof Task) {

            $project = $object->getProject();
            if ($channel = $project->getCustomField1()) {

                $channel_id = rocketchat_get_channel_id_by_name($channel);

                $task_id    = $object->getTaskId();
                $task_name  = $object->getName();
                $task_url   = $object->getViewUrl();

                $user_name  = $object->getUpdatedByName();

                if ($rocketchat_user = rocketchat_get_user_by_email($object->getCompletedByEmail())) {
                    $user_name = "<@{$rocketchat_user['username']}>";
                }

                $msg = "*<{$task_url}|#{$task_id}: {$task_name}>* reopened *{$user_name}*";

                $rocketchat->call('chat.sendMessage', array(
                        'message' => array(
                        'rid'     => $channel_id,
                        'alias'   => 'ActiveCollab',
                        'avatar'  => defined('ASSETS_URL') ? ASSETS_URL . '/images/system/default/application-branding/logo.40x40.png'  : '',
                        'msg'     => $msg
                    )
                ));
            }
        }
    }
}
