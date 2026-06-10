<?php
include('action_object.php');

// --- Site settings ---
$site_title_res = mysqli_fetch_assoc($data->web_setting_details('web_settings', '1'));
$site_url_res   = mysqli_fetch_assoc($data->web_setting_details('web_settings', '197'));

$site_url = $site_url_res['field_value'];

// --- Pagination setup ---
$baseURL = 'get_pagination_matches.php';
$offset  = !empty($_POST['page']) ? (int)$_POST['page'] : 0;
$limit   = 50;

$filters = [];

// --- Basic search ---
if (!empty($_POST['search_value_1'])) {
    $search = $_POST['search_value_1'];
    $filters['search'] = $search;
}

// --- Date range ---
if (!empty($_POST['search_value_2']) && !empty($_POST['search_value_3'])) {
    $filters['date_range'] = [
        'start' => $_POST['search_value_2'],
        'end'   => $_POST['search_value_3']
    ];
}

// --- Team ---
if (!empty($_POST['search_value_4'])) {
    $filters['team'] = $_POST['search_value_4'];
}

// --- Ground ---
if (!empty($_POST['search_value_5'])) {
    $filters['ground'] = $_POST['search_value_5'];
}

// --- Competition ---
if (!empty($_POST['search_value_6'])) {
    $filters['competition'] = $_POST['search_value_6'];
}

// --- Round ---
if (!empty($_POST['search_value_7'])) {
    $filters['round'] = $_POST['search_value_7'];
}

// --- Years ---
if (!empty($_POST['search_value_8'])) {
    $filters['years'] = $_POST['search_value_8'];
}

//----ASC DESC

if (!empty($_POST['match_date_sort'])) {
    $filters['date_flow'] = $_POST['match_date_sort'];
}


// --- Count total rows ---
$rowCount = $data->count_filtered_search_matches($filters);

// --- Pagination ---
$pagConfig = [
    'baseURL'    => $baseURL,
    'totalRows'  => $rowCount,
    'perPage'    => $limit,
    'currentPage'=> $offset,
    'contentDiv' => 'match_record_container'
];
$pagination = new Pagination($pagConfig);

// --- Fetch matches ---
$query = $data->get_filtered_search_matches($filters, $offset, $limit);
$match_record_rowcount = mysqli_num_rows($query);

if (!$query) {
    echo "<p>Error retrieving match records: " . mysqli_error($data->conn) . "</p>";
    exit;
}

$match_record_rowcount = mysqli_num_rows($query);
?>

<!-- ==================== TABLE ==================== -->
<div class="result_filter">
    <div class="left_rusult_pre">
        <p><?php echo $rowCount; ?> Results</p>
    </div>
    <div class="right_reset_but_matches">
        <a href="javascript:void(0);">Reset all filters<span class="glyphicon glyphicon-remove"></span></a>
    </div>
</div>

<table id="match_table">
<thead>
    <tr>
        <th id="match-date-sort" style="cursor:pointer;">
            <div class="text_mn">
                <span class="text_head">Match Date </span>
                <div class="text_bdy">
                    <span id="match-date-up" style="cursor:pointer;">▲</span>
                    <span id="match-date-down" class="active_count" style="cursor:pointer;">▼</span>
                </div>
            </div>
        </th>
        <th>Match Details</th>
        <th>Umpires</th>
        <th>Ground</th>
        <th>Comp</th>
        <th>Round</th>
    </tr>
</thead>
<tbody>
<?php
if ($match_record_rowcount > 0) {
    while ($match_record_row = mysqli_fetch_assoc($query)) {
        $matches_record_db_id = $match_record_row['matches_record_db_id'];
        $matches_record_date = $match_record_row['matches_record_date'];
        $formatted_date = date("d/m/Y", strtotime($matches_record_date));

        $matches_record_home_team_id   = $match_record_row['matches_record_home_team_id'];
        $matches_record_home_team      = $match_record_row['matches_record_home_team'];
        $matches_record_goals1         = $match_record_row['matches_record_goals1'];
        $matches_record_behinds1       = $match_record_row['matches_record_behinds1'];
        $matches_record_points1        = $match_record_row['matches_record_points1'];

        $matches_record_away_team_id   = $match_record_row['matches_record_away_team_id'];
        $matches_record_away_team      = $match_record_row['matches_record_away_team'];
        $matches_record_goals2         = $match_record_row['matches_record_goals2'];
        $matches_record_behinds2       = $match_record_row['matches_record_behinds2'];
        $matches_record_points2        = $match_record_row['matches_record_points2'];

        // Umpire IDs
        $matches_record_umpire_field1_id    = $match_record_row['matches_record_umpire_field1_id'];
        $matches_record_umpire_field2_id    = $match_record_row['matches_record_umpire_field2_id'];
        $matches_record_umpire_field3_id    = $match_record_row['matches_record_umpire_field3_id'];
        $matches_record_umpire_field4_id    = $match_record_row['matches_record_umpire_field4_id'];

        $matches_record_umpire_boundary1_id = $match_record_row['matches_record_umpire_boundary1_id'];
        $matches_record_umpire_boundary2_id = $match_record_row['matches_record_umpire_boundary2_id'];
        $matches_record_umpire_boundary3_id = $match_record_row['matches_record_umpire_boundary3_id'];
        $matches_record_umpire_boundary4_id = $match_record_row['matches_record_umpire_boundary4_id'];
        $matches_record_umpire_boundary5_id = $match_record_row['matches_record_umpire_boundary5_id'];
        $matches_record_umpire_boundary6_id = $match_record_row['matches_record_umpire_boundary6_id'];

        $matches_record_umpire_goal1_id     = $match_record_row['matches_record_umpire_goal1_id'];
        $matches_record_umpire_goal2_id     = $match_record_row['matches_record_umpire_goal2_id'];

        $matches_record_venue = $match_record_row['matches_record_venue'];
        $matches_record_competition = $match_record_row['matches_record_competition'];
        $matches_record_competition_id = $match_record_row['matches_record_competition_id'];
        $matches_record_round = $match_record_row['matches_record_round'];

        $home_winner = $matches_record_points1 > $matches_record_points2;
        $away_winner = $matches_record_points2 > $matches_record_points1;

        // Start row
        echo "<tr>";
        // 1) Match Date
        echo "<td class='date'>{$formatted_date}</td>";

        // 2) Match Details (home + away)
        echo "<td class='team_vs'>
                <div class='match_dtls'>";

        // --- HOME TEAM DETAILS ---
        $team_details = $data->aflua_all_team_details('afl_teams', $matches_record_home_team_id, 1, 1);
        if (mysqli_num_rows($team_details) > 0) {
            $team_details_row = mysqli_fetch_assoc($team_details);
            $home_team_image = $team_details_row['team_color_image'];

            echo "<div class='flag_tem'><img src='{$site_url}{$home_team_image}' alt='Home Team'></div>
                  <div class='cont_dtls " . ($home_winner ? 'active_black' : '') . "'>
                    <span class='team_name'>" . ucwords($matches_record_home_team) . "</span>
                    <div class='mai_rig'>
                      <span class='goal'>{$matches_record_goals1}</span>.<span class='be_goal'>{$matches_record_behinds1}</span>
                      (<span class='point'>{$matches_record_points1}</span>)
                    </div>
                  </div>";
        }

        echo "</div>
              <div class='match_dtls'>";

        // --- AWAY TEAM DETAILS ---
        $team_details = $data->aflua_all_team_details('afl_teams', $matches_record_away_team_id, 1, 1);
        if (mysqli_num_rows($team_details) > 0) {
            $team_details_row = mysqli_fetch_assoc($team_details);
            $away_team_image = $team_details_row['team_color_image'];

            echo "<div class='flag_tem'><img src='{$site_url}{$away_team_image}' alt='Away Team'></div>
                  <div class='cont_dtls " . ($away_winner ? 'active_black' : '') . "'>
                    <span class='team_name'>" . ucwords($matches_record_away_team) . "</span>
                    <div class='mai_rig'>
                      <span class='goal'>{$matches_record_goals2}</span>.<span class='be_goal'>{$matches_record_behinds2}</span>
                      (<span class='point'>{$matches_record_points2}</span>)
                    </div>
                  </div>";
        }

        echo "</div></td>";

        // 3) Umpires (keep structure from your original code)
        echo "<td>
                <div class='umpir_std'>
                  <ul>";

        // FIELD UMPIRES
        echo "<li>";
        $field_umpires = [
            1 => $matches_record_umpire_field1_id,
            2 => $matches_record_umpire_field2_id,
            3 => $matches_record_umpire_field3_id,
            4 => $matches_record_umpire_field4_id
        ];
        foreach ($field_umpires as $num => $umpire_id) {
            if (!empty($umpire_id)) {
                $umpire_details = $data->aflua_all_umpire_details_by_id_fetch('afl_umpires', $umpire_id, 1, 1);
                if (mysqli_num_rows($umpire_details) > 0) {
                    $umpire = mysqli_fetch_assoc($umpire_details);
                    echo "<span>Field : <a href='javascript:void(0)' class='umpire_link' data-umpire-id='" . htmlspecialchars($umpire_id) . "'>" . ucwords($umpire['umpire_name']) . "</a></span>";
                }
            }
        }
        echo "</li>";

        // BOUNDARY UMPIRES
        echo "<li>";
        $boundary_umpires = [
            1 => $matches_record_umpire_boundary1_id,
            2 => $matches_record_umpire_boundary2_id,
            3 => $matches_record_umpire_boundary3_id,
            4 => $matches_record_umpire_boundary4_id,
            5 => $matches_record_umpire_boundary5_id,
            6 => $matches_record_umpire_boundary6_id
        ];
        foreach ($boundary_umpires as $num => $umpire_id) {
            if (!empty($umpire_id)) {
                $umpire_details = $data->aflua_all_umpire_details_by_id_fetch('afl_umpires', $umpire_id, 1, 1);
                if (mysqli_num_rows($umpire_details) > 0) {
                    $umpire = mysqli_fetch_assoc($umpire_details);
                    echo "<span>Boundary : <a href='javascript:void(0)' class='umpire_link' data-umpire-id='" . htmlspecialchars($umpire_id) . "'>" . ucwords($umpire['umpire_name']) . "</a></span>";
                }
            }
        }
        echo "</li>";

        // GOAL UMPIRES
        echo "<li>";
        $goal_umpires = [
            1 => $matches_record_umpire_goal1_id,
            2 => $matches_record_umpire_goal2_id
        ];
        foreach ($goal_umpires as $num => $umpire_id) {
            if (!empty($umpire_id)) {
                $umpire_details = $data->aflua_all_umpire_details_by_id_fetch('afl_umpires', $umpire_id, 1, 1);
                if (mysqli_num_rows($umpire_details) > 0) {
                    $umpire = mysqli_fetch_assoc($umpire_details);
                    echo "<span>Goal : <a href='javascript:void(0)' class='umpire_link' data-umpire-id='" . htmlspecialchars($umpire_id) . "'>" . ucwords($umpire['umpire_name']) . "</a></span>";
                }
            }
        }
        echo "</li>";

        echo "    </ul>
                </div>
              </td>";

        // 4) Ground
        echo "<td>";
        if (!empty($matches_record_venue)) {
            echo ucwords($matches_record_venue);
        }
        echo "</td>";

        // 5) Competition
        echo "<td>";
        if (!empty($matches_record_competition_id)) {

            $comp_details = $data->aflua_all_competetions_type_by_id('competition_type',$matches_record_competition_id,1);

            $comp_details_row = mysqli_fetch_assoc($comp_details);
            $comp_name = $comp_details_row['competition_type_name'];
            echo "<span class='comp_col " . htmlspecialchars($comp_name) . "'>" . strtoupper($comp_name) . "</span>";
        }
        echo "</td>";

        // 6) Round
        echo "<td>";
        if (!empty($matches_record_round)) {
            echo htmlspecialchars($matches_record_round);
        }
        echo "</td>";

        echo "</tr>";
    }
}
?>
</tbody>
</table>

<!-- ==================== PAGINATION ==================== -->
<?php
$totalPages = ceil($rowCount / $limit);
if ($totalPages > 1) {
    echo '<div class="pagination">';

    // Previous
    $prev = max(0, $offset - $limit);
    if ($offset > 0) {
        echo "<button><a href='#' data-page='{$prev}' class='prev'><img src='{$site_url}/assets/images/left-arrow.png' alt='Previous'>Previous</a></button>";
    } else {
        echo "<button class='prev' disabled><img src='{$site_url}/assets/images/left-arrow.png' alt='Previous'>Previous</button>";
    }

    // Page numbers
    // echo '<div class="pages pagination_desktop">';
    // for ($i = 0; $i < $totalPages; $i++) {
    //     $pageNum = $i + 1;
    //     $pageOffset = $i * $limit;
    //     if ($pageOffset == $offset) {
    //         echo "<span class='active'>{$pageNum}</span>";
    //     } else {
    //         echo "<span><a href='#' data-page='{$pageOffset}'>{$pageNum}</a></span>";
    //     }
    // }
    // echo '</div>';


    //dots
    echo '<div class="pagination_mobile">';
    $currentPage = floor($offset / $limit) + 1;
    $range = 1; // pages before & after current
    $firstPages = 2;
    $lastPages = 2;

    $ellipsisShownLeft = false;
    $ellipsisShownRight = false;

    for ($i = 1; $i <= $totalPages; $i++) {

        $pageOffset = ($i - 1) * $limit;

        $show =
            $i <= $firstPages ||                             // first pages
            $i > $totalPages - $lastPages ||                 // last pages
            abs($i - $currentPage) <= $range;                // near current

        if ($show) {
            if ($pageOffset == $offset) {
                echo "<span class='active'>{$i}</span>";
            } else {
                echo "<span><a href='#' data-page='{$pageOffset}'>{$i}</a></span>";
            }
        } else {
            if ($i < $currentPage && !$ellipsisShownLeft) {
                echo "<span class='dots'>…</span>";
                $ellipsisShownLeft = true;
            }
            if ($i > $currentPage && !$ellipsisShownRight) {
                echo "<span class='dots'>…</span>";
                $ellipsisShownRight = true;
            }
        }
    }

    echo '</div>';

    // Next
    $next = min(($totalPages - 1) * $limit, $offset + $limit);
    if ($offset + $limit < $rowCount) {
        echo "<button><a href='#' data-page='{$next}' class='next'>Next<img src='{$site_url}/assets/images/left-arrow.png' alt='next'></a></button>";
    } else {
        echo "<button class='next' disabled>Next<img src='{$site_url}/assets/images/left-arrow.png' alt='next'></button>";
    }

    echo '</div>';
}
?>
