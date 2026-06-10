<?php
include('action_object.php');

// --- Site settings ---
$site_title_res = mysqli_fetch_assoc($data->web_setting_details('web_settings', '1'));
$site_url_res   = mysqli_fetch_assoc($data->web_setting_details('web_settings', '197'));
$site_url = $site_url_res['field_value'];

// --- Pagination setup ---
$baseURL = '_get_umpire_profile_action.php';
$offset  = !empty($_POST['page']) ? (int)$_POST['page'] : 0;
$limit   = 50;

$filters = [];

// --- Basic search ---
if (!empty($_POST['search_value_1'])) {
    $search_id = $_POST['search_value_1'];
    $filters['search'] = $search_id;
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
    $filters['year'] = $_POST['search_value_8'];
}


//--- Discipline -------

if (!empty($_POST['search_value_9'])) {
    $filters['discipline'] = $_POST['search_value_9'];
}

// --- Count total rows ---
$rowCount = $data->count_filtered_matches($filters);

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
$query = $data->get_filtered_matches($filters, $offset, $limit);
$match_record_rowcount = mysqli_num_rows($query);

?>

<!-- ==================== TABLE ==================== -->
<div class="result_filter">
    <div class="left_rusult_pre">
        <p><?php echo $rowCount; ?> Results</p>
    </div>
    <div class="right_reset_but_umpire_profile right_reset_but">
        <a href="javascript:void(0);">Reset all filters<span class="glyphicon glyphicon-remove"></span></a>
    </div>
</div>

<table id="umpire_profil_table">
<thead>
    <tr>
        <th>Match Date</th>
        <th>Match Details</th>
        <th>Discipline</th>
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

        $discipline_html = '';
        if($matches_record_umpire_field1_id == $search_id){
            $discipline_html = 'Field';
        }
        elseif($matches_record_umpire_field2_id == $search_id){
            $discipline_html = 'Field';
        }
        elseif($matches_record_umpire_field3_id == $search_id){
            $discipline_html = 'Field';
        }
        elseif($matches_record_umpire_field4_id == $search_id){
            $discipline_html = 'Field';
        }
        elseif($matches_record_umpire_boundary1_id == $search_id){
            $discipline_html = 'Boundary';
        }
        elseif($matches_record_umpire_boundary2_id == $search_id){
            $discipline_html = 'Boundary';
        }
        elseif($matches_record_umpire_boundary3_id == $search_id){
            $discipline_html = 'Boundary';
        }
        elseif($matches_record_umpire_boundary4_id == $search_id){
            $discipline_html = 'Boundary';
        }
        elseif($matches_record_umpire_boundary5_id == $search_id){
            $discipline_html = 'Boundary';
        }
        elseif($matches_record_umpire_boundary6_id == $search_id){
            $discipline_html = 'Boundary';
        }
        elseif($matches_record_umpire_goal1_id == $search_id){
            $discipline_html = 'Goal';
        }
        elseif($matches_record_umpire_goal2_id == $search_id){
            $discipline_html = 'Goal';
        }

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

        // echo "<td>";
        // if (!empty($search_id)) {
        //     $umpire_details = $data->aflua_all_umpire_details_by_id_fetch('afl_umpires', $search_id, 1, 1);
        //     if (mysqli_num_rows($umpire_details) > 0) {
        //         $umpire = mysqli_fetch_assoc($umpire_details);
        //         echo ucwords($umpire['umpire_type']);                               
        //     }
        // }
        // echo "</td>";

        echo "<td>{$discipline_html}</td>";

        // 4) Ground
        echo "<td>";
        if (!empty($matches_record_venue)) {
            echo ucwords($matches_record_venue);
        }
        echo "</td>";

        // 5) Competition
        echo "<td>";
        if (!empty($matches_record_competition)) {
            echo "<span class='comp_col " . htmlspecialchars($matches_record_competition) . "'>" . strtoupper($matches_record_competition) . "</span>";
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


