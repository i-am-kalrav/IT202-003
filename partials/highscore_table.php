<?php
//requires functions.php
//requires a duration to be set
if (!isset($duration)) {
    //$duration = "day"; //choosing to default to day
    $duration = "week"; //choosing to default to week
}
$results = get_top_10($duration);

switch ($duration) {
    /*case "day":
        $title = "Top Scores Today";
        break;*/
    case "week":
        $title = "Top Scores This Week";
        break;
    case "month":
        $title = "Top Scores This Month";
        break;
    case "lifetime":
        $title = "All Time Top Scores";
        break;
    default:
        $title = "Invalid Scoreboard";
        break;
}
?>

<div class="card">
    <div class="card-body">
        <div class="card-title">
            <div class="fw-bold fs-3">
                <?php se($title); ?>
            </div>
        </div>
        <div class="card-text">
            <th class="nav-item"><a class="nav-link" href="game.php">Play Game</a></th>
            <table class="table">
                <thead>
                    <th>User</th>
                    <th>Score</th>
                    <th>Date</th>
                </thead>
                <tbody>
                    <?php if (!$results || count($results) == 0) : ?>
                        <tr>
                            <td>No scores available yet</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($results as $result) : ?>
                            <tr>
                                <td>
                                    <a href="profile.php?id=<?php se($result, 'user_id'); ?>"><?php se($result, "username"); ?></a>
                                </td>
                                <td><?php se($result, "score"); ?></td>
                                <td><?php se($result, "created"); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
