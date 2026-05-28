<?php
$u = App\Models\User::where('email', 'manilukka143@gmail.com')->first();
$u->password = 'Mani@098';
$u->save();
