<?php

/**
 * @Author: Anton Baranov
 * @Author: Dmitrij Omelchuk
 * @Last Modified by:   Anton Baranov
 * @Last Modified time: 2020-05-12 20:28:29
 */

function rocketchat_handle_on_object_inserted($object){
    if (defined('ROCKETCHAT_TOKEN') and defined('ROCKETCHAT_URL') and defined('ROCKETCHAT_USERID')) {

        $rocketchat = new RocketChat(ROCKETCHAT_URL, ROCKETCHAT_TOKEN, ROCKETCHAT_USERID);

        if ($object instanceof Task) {

            $project = $object->getProject();
            if ($target = $project->getCustomField1()) {

                if($target[0] === '#'){
                    $target_id = rocketchat_get_channel_id_by_name($target);
                } else {
                    $target_id = rocketchat_get_group_id_by_name($target);
                }

                $task_id    = $object->getTaskId();
                $task_name  = $object->getName();
                $task_url   = $object->getViewUrl();

                $user_cr = $object->getCreatedBy();
                $user_cr_n = $user_cr->getName();

                if ($rocketchat_user = rocketchat_get_user_by_email($user_cr->getEmail())) {
                    $user_cr_n = "<@{$rocketchat_user['username']}>";
                }

                $user_as = $object->assignees()->getAssignee();

                if ($user_as) {
                    $user_as_n = $user_as->getName();
                    if ($rocketchat_user = rocketchat_get_user_by_email($user_as->getEmail())) {
                        $user_as_n = "<@{$rocketchat_user['username']}>";
                    }
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

                $msg = "Task *<{$task_url}|#{$task_id}: {$task_name}>* created by *{$user_cr_n}*\n\n>>>{$text}";

                $rocketchat->call('chat.sendMessage', array(
                    'message' => array(
                        'rid' => $channel_id,
                        'alias'  => 'ActiveCollab',
                        'avatar'  => defined('ASSETS_URL') ? ASSETS_URL . '/images/system/default/application-branding/logo.40x40.png'  : '',
                        'msg' => $msg
                    )
                ));

            }
        }

        if ($object instanceof TaskComment) {

            $project = $object->getProject();
            if ($target = $project->getCustomField1()) {

                if($target[0] === '#'){
                    $target_id = rocketchat_get_channel_id_by_name($target);
                } else {
                    $target_id = rocketchat_get_group_id_by_name($target);
                }

                $task = $object->getParent();
                $task_id = $task->getTaskId();
                $task_name = $task->getName();
                $task_url = $task->getViewUrl();

                $user = $object->getCreatedBy();
                $user_name = $user->getName();

                if ($rocketchat_user = rocketchat_get_user_by_email($user->getEmail())) {
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

                $msg = "*{$user_name}* add comment to: *<{$task_url}|#{$task_id}: {$task_name}>*\n\n>>>{$text}";
                $rocketchat->call('chat.sendMessage', array(
                    'message' => array(
                        'rid' => $channel_id,
                        'alias'  => 'ActiveCollab',
                        'avatar'  => defined('ASSETS_URL') ? ASSETS_URL . '/images/system/default/application-branding/logo.40x40.png'  : '',
                        'msg' => $msg
                    )
                ));
            }
        }

        if ($object instanceof Subtask) {

            $project = $object->getProject();
            if ($target = $project->getCustomField1()) {

                if($target[0] === '#'){
                    $target_id = rocketchat_get_channel_id_by_name($target);
                } else {
                    $target_id = rocketchat_get_group_id_by_name($target);
                }

                $task = $object->getParent();
                $task_id = $task->getTaskId();
                $task_name = $task->getName();
                $task_url = $task->getViewUrl();

                $user = $object->getCreatedBy();
                $user_name = $user->getName();

                if ($rocketchat_user = rocketchat_get_user_by_email($user->getEmail())) {
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

                $msg = "*{$user_name}* add subtask in *<{$task_url}|#{$task_id}: {$task_name}>*\n\n>>>{$text}";
                $rocketchat->call('chat.sendMessage', array(
                    'message' => array(
                        'rid' => $channel_id,
                        'alias'  => 'ActiveCollab',
                        'avatar'  => defined('ASSETS_URL') ? ASSETS_URL . '/images/system/default/application-branding/logo.40x40.png'  : '',
                        'msg' => $msg
                    )
                ));
            }
        }

    }

}
