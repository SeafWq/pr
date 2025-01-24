<?php

include_once('db.php');
include_once('model.php');

$user_id = isset($_GET['user'])
    ? (int)$_GET['user']
    : null;

if ($user_id) {
    // Get transactions balances
    $transactions = get_user_monthly_balances($user_id, $conn);
    // TODO: implement
}

if (isset($_GET['user'])) {
    $user_id = (int)$_GET['user'];
    $monthly_balances = get_user_monthly_balances($user_id, $conn);
    $user_name = '';

    // Получаем имя пользователя для отображения
    $users = get_users($conn);
    foreach ($users as $user) {
        if ($user['id'] == $user_id) {
            $user_name = $user['name'];
            break;
        }
    }
    ?>
    <div id="data">
        <h2>Transactions of <?php echo htmlspecialchars($user_name); ?></h2>
        <table>
            <tr>
                <th>Month</th>
                <th>Balance</th>
            </tr>
            <?php
            foreach ($monthly_balances as $month_year => $balance) {
                // Разделяем год и месяц
                list($year, $month) = explode('-', $month_year);
                $month_name = date("F", mktime(0, 0, 0, $month, 1)); // Получаем название месяца
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($month_name . ' ' . $year); ?></td>
                    <td><?php echo htmlspecialchars($balance); ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>
    <?php
}
?>
