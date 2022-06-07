<?php

use App\Models\Activity;

function pushActivity($user, $pointer, $type) {
    try {
        $data = [];
        $data['user_id'] = $user;
        $data['pointer_id'] = $pointer;
        $data['type'] = $type;

        Activity::create($data);

        return true;

    } catch (\Exception $e) {
        return false;
    }
}
