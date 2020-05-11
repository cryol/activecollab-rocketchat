<?php

/**
 * @Author: Anton Baranov
 * @Date:   2020-05-10 22:11:30
 * @Last Modified by:   Anton Baranov
 * @Last Modified time: 2020-05-11 13:31:36
 */

function rocketchat_handle_on_object_completed($object) {
    if (defined('ROCKETCHAT_TOKEN') and defined('ROCKETCHAT_URL') and defined('ROCKETCHAT_USERID')) {

        $rocketchat = new RocketChat(ROCKETCHAT_URL, ROCKETCHAT_TOKEN, ROCKETCHAT_USERID);

        if ($object instanceof Task) {

            $project = $object->getProject();
            if ($channel = $project->getCustomField1()) {

                $channel_id = rocketchat_get_channel_id_by_name($channel);

                $task_id    = $object->getTaskId();
                $task_name  = $object->getName();
                $task_url   = $object->getViewUrl();

                $user_name  = $object->getCompletedByName();

                if ($rocketchat_user = rocketchat_get_user_by_email($object->getCompletedByEmail())) {
                    $user_name = "<@{$rocketchat_user['username']}>";
                }

                $msg = "*<{$task_url}|#{$task_id}: {$task_name}>* completed *{$user_name}*";

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
        if ($object instanceof Subtask) {

            $project = $object->getProject();
            if ($channel = $project->getCustomField1()) {

                $channel_id = rocketchat_get_channel_id_by_name($channel);

                $task = $object->getParent();
                $task_id = $task->getTaskId();
                $task_name = $task->getName();
                $task_url = $task->getViewUrl();

                $user_name  = $object->getCompletedByName();

                if ($rocketchat_user = rocketchat_get_user_by_email($object->getCompletedByEmail())) {
                    $user_name = "<@{$rocketchat_user['username']}>";
                }

                $body = $object->getBody();
                $br = array("<br>", "</div>", "</p>");
                $body = str_replace($br, "\n", $body);
                $body = str_replace("&nbsp;", " ", $body);
                $body = str_replace("&amp;", "&", $body);
                $body = str_replace("<li>", "\n• ", $body);
                $body = str_replace("</ul>", "\n\n", $body);
                $body = preg_replace('/<strong>(\s*)/', '$1*', $body);
                $body = preg_replace('/(\s*)<\/strong>/', '*$1', $body);
                $body = preg_replace('/<em>(\s*)/', '$1_', $body);
                $body = preg_replace('/(\s*)<\/em>/', '_$1', $body);
                $body = preg_replace('/<blockquote>(\s*)/', "```", $body);
                $body = preg_replace('/(\s*)<\/blockquote>/', "```\n", $body);
            //  $body  = preg_replace('/<a href="(.+?)".+ alt="(.+?)">/', '$1', $body);
                $body = preg_replace('/(^.+)<div class="object_attachments attachments".*/', '$1', $body);
                $body = preg_replace('/<img src="(.+?)" alt="(.+?)">/', '$1', $body);
                $text = strip_tags($body);

                $msg = "*{$user_name}* complete subtask in *<{$task_url}|#{$task_id}: {$task_name}>*\n\n>>>{$text}";

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
