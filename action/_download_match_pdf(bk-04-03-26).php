<?php
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
                <th style="padding: 0 60px;">Match Details</th>
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
        
                <div class='match_dtls' style='width:50px; font-size: 14px; padding-bottom: 6px;'>";

        // --- HOME TEAM DETAILS ---
        $team_details = $data->aflua_all_team_details('afl_teams', $matches_record_home_team_id, 1, 1);
        if (mysqli_num_rows($team_details) > 0) {
            $team_details_row = mysqli_fetch_assoc($team_details);
            $home_team_image = $team_details_row['team_color_image'];
        
                        $html .= "<img style='height: 20px;' src='{$site_url}{$home_team_image}' alt='Home Team'>
                    <span style='padding-right: 25px; font-size: 13px;'>" . ucwords($matches_record_home_team) . "               
                      <span style='font-size: 13px;'>{$matches_record_goals1}</span>.<span style='font-size: 13px;'>{$matches_record_behinds1}</span> (<span style='font-size: 13px;'>{$matches_record_points1}</span>)</span> " ;
            
            
            
        }

        $html .= "</div>";
        
        $html .= "<div style='font-size:3px;'>$</div>";
        
        $html .= "<div style='padding: 6px; margin-top: 6px;'>";
        // --- AWAY TEAM DETAILS ---
        $team_details = $data->aflua_all_team_details('afl_teams', $matches_record_away_team_id, 1, 1);
        if (mysqli_num_rows($team_details) > 0) {
            $team_details_row = mysqli_fetch_assoc($team_details);
            $away_team_image = $team_details_row['team_color_image'];

            $html .= "<img style='height: 20px;' src='{$site_url}{$away_team_image}' alt='Away Team'>
                    <span style='margin-right: 25px; font-size: 13px;'>" . ucwords($matches_record_away_team) . "
                      <span style='font-size: 13px;'>{$matches_record_goals2}</span>.<span style=''>{$matches_record_behinds2}</span>
                      (<span style=''>{$matches_record_points2}</span>)</span>";
        }

        $html .= "</div></td>";

        // 3) Umpires (keep structure from your original code)
        $html .= "<td>
                <div class='umpir_std'>
                  <div>";

        // FIELD UMPIRES
        $html .= "<div style='height: 50px;'>";
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
                    $html .= "<span style='margin-right: 5px; text-decoration: none; font-size: 12px; font-weight: 500;'>Field {$num}: <span style='color: #087443; font-weight: 600; font-size: 14px;'>" . ucwords($umpire['umpire_name']) . "</span></span><span>2</span>";
                }
            }
        }
        $html .= "</div>";

        // BOUNDARY UMPIRES
        $html .= "<div>";
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
                    $html .= "<span style='text-decoration: none; font-size: 12px; font-weight: 500;'>Boundary {$num}: <span style='color: #087443; font-weight: 600; font-size: 14px;'>" . ucwords($umpire['umpire_name']) . "</span></span>";
                }
            }
        }
        $html .= "</div>";

        // GOAL UMPIRES
        $html .= "<div>";
        $goal_umpires = [
            1 => $matches_record_umpire_goal1_id,
            2 => $matches_record_umpire_goal2_id
        ];
        foreach ($goal_umpires as $num => $umpire_id) {
            if (!empty($umpire_id)) {
                $umpire_details = $data->aflua_all_umpire_details_by_id_fetch('afl_umpires', $umpire_id, 1, 1);
                if (mysqli_num_rows($umpire_details) > 0) {
                    $umpire = mysqli_fetch_assoc($umpire_details);
                    $html .= "<span style='text-decoration: none;  font-size: 12px; font-weight: 500;'>Goal {$num}: <span style='color: #087443; font-weight: 600; font-size: 14px;'>" . ucwords($umpire['umpire_name']) . "</span></span>";
                }
            }
        }
        $html .= "</div>";

        $html .= "    </div>
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
        if (!empty($matches_record_competition)) {
            $html .= "<span class='comp_col " . htmlspecialchars($matches_record_competition) . "'>" . strtoupper($matches_record_competition) . "</span>";
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
