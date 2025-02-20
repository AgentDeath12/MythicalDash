<?php
include(__DIR__ . '/../requirements/page.php');

if (isset($_GET['userid']) && isset($_GET['coins']) && is_numeric($_GET['coins'])) {
    $userid = mysqli_real_escape_string($conn, $_GET['userid']);
    $coins = intval($_GET['coins']);

    $userQuery = "SELECT * FROM mythicaldash_users WHERE `id` = '".mysqli_real_escape_string($conn,$userid)."'";
    $userResult = mysqli_query($conn, $userQuery);

    if (mysqli_num_rows($userResult) > 0) {
        if ($session->getUserInfo("id") == $_GET['userid']) {
            header("location: /user/profile?e=".$lang['you_cant_send_coins_to_yourself']."!&id=" . $_GET['userid']);
            die();
        }
        if ($coins <= 0) {
            header("location: /user/profile?e=".$lang['input_not_valid']."id=" . $_GET['userid']);
            die();
        }
        if ($coins <= $session->getUserInfo("coins")) {
            $giftUserQuery = "SELECT * FROM mythicaldash_users WHERE id = '".mysqli_real_escape_string($conn,$userid)."'";
            $giftUserResult = mysqli_query($conn, $giftUserQuery);
            $giftUser = mysqli_fetch_assoc($giftUserResult);

            $u_new_coins = $session->getUserInfo("coins") - $coins;
            $g_new_coins = $giftUser['coins'] + $coins;

            $updateGiftUserQuery = "UPDATE `mythicaldash_users` SET `coins` = '".mysqli_real_escape_string($conn,$g_new_coins)."' WHERE `id` = {$giftUser['id']}";
            $updateSenderQuery = "UPDATE `mythicaldash_users` SET `coins` = '".mysqli_real_escape_string($conn,$u_new_coins)."' WHERE `id` = {$session->getUserInfo("id")}";

            mysqli_query($conn, $updateSenderQuery);
            mysqli_query($conn, $updateGiftUserQuery);

            header("location: /user/profile?id={$giftUser['id']}&s=Sent $coins coin(s) to {$giftUser['username']}");
            die();
        } else {
            header("location: /user/profile?e=".$lang['store_not_have_enough_coins']."&id=$userid");
            die();
        }
    } else {
        header("location: /dashboard?e=".$lang['error_not_found_in_database']);
        die();
    }
} else {
    header("location: /dashboard?e=".$lang['input_not_valid']);
    die();
}
?>