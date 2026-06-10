<?php

// set_time_limit(600); // 10 minutes
// ini_set('memory_limit', '2048M'); // 2GB
// ini_set('max_execution_time', 600);
include('action_object.php');

$site_title_res = mysqli_fetch_assoc($data->web_setting_details('web_settings', '1'));
$site_url_res   = mysqli_fetch_assoc($data->web_setting_details('web_settings', '197'));
$site_url = $site_url_res['field_value'];
// Collect filter values
$limit  = 999999;
$offset = 0;
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


// --- years ---
if (!empty($_POST['search_value_8'])) {
    $filters['years'] = $_POST['search_value_8'];
}


//----ASC DESC

if (!empty($_POST['date_flow'])) {
    $filters['date_flow'] = $_POST['date_flow'];
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
    $html .= "<p>Error retrieving match records: " . mysqli_error($data->conn) . "</p>";
    exit;
}

$match_record_rowcount = mysqli_num_rows($query);

$html = '<h2 style="text-align:center;">Match List</h2>
        <div class="result_filter">
            <div class="left_rusult_pre">
                <p>'.$rowCount.' Results</p>
            </div>
        </div>

        <table id="match_table" border="1" cellpadding="8" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Match Date</th>
                <th>Match Details</th>
                <th>Umpires</th>
                <th>Ground</th>
                <th>Comp</th>
                <th>Round</th>
            </tr>
        </thead>
        <tbody>';

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
        $html .= "<tr>";
        // 1) Match Date
        $html .= "<td class='date'>{$formatted_date}</td>";

        // 2) Match Details (home + away)
        $html .= "<td class='team_vs'>
                <div class='match_dtls'>";

        // --- HOME TEAM DETAILS ---
        $team_details = $data->aflua_all_team_details('afl_teams', $matches_record_home_team_id, 1, 1);
        if (mysqli_num_rows($team_details) > 0) {
            $team_details_row = mysqli_fetch_assoc($team_details);
            $home_team_image = $team_details_row['team_color_image'];
        
                        $html .= "<img style='height: 20px;' src='{$site_url}{$home_team_image}' alt='Home Team'>
                        
                       
                    <span class='team_name'>" . ucwords($matches_record_home_team) . "</span>
                    
                      <span class='goal'>{$matches_record_goals1}</span>.<span class='be_goal'>{$matches_record_behinds1}</span>
                      (<span class='point'>{$matches_record_points1}</span>)
                    " ;
            
            
            
        }

        $html .= "</div>
              <div class='match_dtls'>";

        // --- AWAY TEAM DETAILS ---
        $team_details = $data->aflua_all_team_details('afl_teams', $matches_record_away_team_id, 1, 1);
        if (mysqli_num_rows($team_details) > 0) {
            $team_details_row = mysqli_fetch_assoc($team_details);
            $away_team_image = $team_details_row['team_color_image'];

            $html .= "<div class='flag_tem'><img src='{$site_url}{$away_team_image}' alt='Away Team'></div>
                  <div class='cont_dtls " . ($away_winner ? 'active_black' : '') . "'>
                    <span class='team_name'>" . ucwords($matches_record_away_team) . "</span>
                    <div class='mai_rig'>
                      <span class='goal'>{$matches_record_goals2}</span>.<span class='be_goal'>{$matches_record_behinds2}</span>
                      (<span class='point'>{$matches_record_points2}</span>)
                    </div>
                  </div>";
        }

        $html .= "</div></td>";

        // 3) Umpires (keep structure from your original code)
        $html .= "<td>
                <div class='umpir_std'>
                  <ul>";

        // FIELD UMPIRES
        $html .= "<li>";
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
                    $html .= "<span>Field : <a href='javascript:void(0)' class='umpire_link' data-umpire-id='" . htmlspecialchars($umpire_id) . "'>" . ucwords($umpire['umpire_name']) . "</a></span>";
                }
            }
        }
        $html .= "</li>";

        // BOUNDARY UMPIRES
        $html .= "<li>";
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
                    $html .= "<span>Boundary : <a href='javascript:void(0)' class='umpire_link' data-umpire-id='" . htmlspecialchars($umpire_id) . "'>" . ucwords($umpire['umpire_name']) . "</a></span>";
                }
            }
        }
        $html .= "</li>";

        // GOAL UMPIRES
        $html .= "<li>";
        $goal_umpires = [
            1 => $matches_record_umpire_goal1_id,
            2 => $matches_record_umpire_goal2_id
        ];
        foreach ($goal_umpires as $num => $umpire_id) {
            if (!empty($umpire_id)) {
                $umpire_details = $data->aflua_all_umpire_details_by_id_fetch('afl_umpires', $umpire_id, 1, 1);
                if (mysqli_num_rows($umpire_details) > 0) {
                    $umpire = mysqli_fetch_assoc($umpire_details);
                    $html .= "<span>Goal : <a href='javascript:void(0)' class='umpire_link' data-umpire-id='" . htmlspecialchars($umpire_id) . "'>" . ucwords($umpire['umpire_name']) . "</a></span>";
                }
            }
        }
        $html .= "</li>";

        $html .= "    </ul>
                </div>
              </td>";

        // 4) Ground
        $html .= "<td>";
        if (!empty($matches_record_venue)) {
            $html .= ucwords($matches_record_venue);
        }
        $html .= "</td>";

        // 5) Competition
        $html .= "<td>";
        if (!empty($matches_record_competition_id)) {
            $comp_details = $data->aflua_all_competetions_type_by_id('competition_type',$matches_record_competition_id,1);

            $comp_details_row = mysqli_fetch_assoc($comp_details);
            $comp_name = $comp_details_row['competition_type_name'];
            $html .= "<span class='comp_col " . htmlspecialchars($comp_name) . "'>" . strtoupper($comp_name) . "</span>";
        }
        $html .= "</td>";

        // 6) Round
        $html .= "<td>";
        if (!empty($matches_record_round)) {
            $html .= htmlspecialchars($matches_record_round);
        }
        $html .= "</td>";

        $html .= "</tr>";
    }
} else {
    $html .= '<tr><td colspan="6" style="text-align:center;">No records found</td></tr>';
}

$html .= '</tbody></table>';

// $test =$site_url.''.$home_team_image;


    // Generate PDF
    $mpdf = new \Mpdf\Mpdf(['format' => [264, 225]]);
    $mpdf->SetHeader("Match List - ".time()." ");
    $mpdf->SetFooter("© Aflua | Page {PAGENO}");
    $mpdf->WriteHTML($html);
    $mpdf->Output("match-list-".time().".pdf", \Mpdf\Output\Destination::DOWNLOAD);
?>
