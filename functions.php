<?php

/**
 * @Author: Anton Baranov
 * @Author: Dmitrij Omelchuk
 * @Last Modified by:   Anton Baranov
 * @Last Modified time: 2020-05-12 20:32:01
 */

function rocketchat_get_user_by_email($email) {
    static $users;
    if (defined('ROCKETCHAT_TOKEN') and defined('ROCKETCHAT_URL') and defined('ROCKETCHAT_USERID')) {
        if (!$users) {
            $rocketchat = new RocketChat(ROCKETCHAT_URL, ROCKETCHAT_TOKEN, ROCKETCHAT_USERID);
            $response = $rocketchat->call_get('users.list');
            if ($response['success']) {
                $users = $response['users'];
            }
            foreach ($users as $user) {
                if(array_key_exists('emails', $user)){
                    foreach ($user['emails'] as $email) {
                        if($email['address'] == $email){
                            return $user;
                        }
                    }
                }
            }
        }
    }
}

function rocketchat_get_channel_id_by_name($name) {
    static $channels;
    if (defined('ROCKETCHAT_TOKEN') and defined('ROCKETCHAT_URL') and defined('ROCKETCHAT_USERID')) {
        if (!$channels) {
            $rocketchat = new RocketChat(ROCKETCHAT_URL, ROCKETCHAT_TOKEN, ROCKETCHAT_USERID);
            $response = $rocketchat->call_get('channels.list');
            if ($response['success']) {
                $channels = $response['channels'];
            }
            foreach ($channels as $channel) {
                if($channel['name'] == ltrim($name, "#")){
                    return $channel['_id'];
                }
            }
        }
    }
}

function rocketchat_get_group_id_by_name($name) {
    static $groups;
    if (defined('ROCKETCHAT_TOKEN') and defined('ROCKETCHAT_URL') and defined('ROCKETCHAT_USERID')) {
        if (!$groups) {
            $rocketchat = new RocketChat(ROCKETCHAT_URL, ROCKETCHAT_TOKEN, ROCKETCHAT_USERID);
            $response = $rocketchat->call_get('groups.list');
            if ($response['success']) {
                $groups = $response['groups'];
            }
            foreach ($groups as $group) {
                if($group['name'] == $name){
                    return $group['_id'];
                }
            }
        }
    }
}
