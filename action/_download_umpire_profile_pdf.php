<?php
include('action_object.php');

$site_title_res = mysqli_fetch_assoc($data->web_setting_details('web_settings', '1'));
$site_url_res   = mysqli_fetch_assoc($data->web_setting_details('web_settings', '197'));
$site_url = $site_url_res['field_value'];
// Collect filter values
$limit  = 9999;
$offset = 0;
$filters = [];

// --- Basic search ---
if (!empty($_POST['search_value_1'])) {
    $search_id = $_POST['search_value_1'];
    $filters['search'] = $search_id;

    $umpire_dets = $data->aflua_all_umpire_details_by_id_fetch('afl_umpires',$search_id,1,1);
                        

    $umpire_row = mysqli_fetch_assoc($umpire_dets);
    $umpire_name = $umpire_row['umpire_name'];
    $umpire_name = str_replace(' ', '-', strtolower($umpire_name));
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

// --- Year ---
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

$profile_query = $data->get_default_matches($search_id, $offset, $limit);
$match_record_profile_rowcount = mysqli_num_rows($profile_query);

$match_record_row_fetch = [];
if ($match_record_profile_rowcount > 0) {  
    while($fetch_row = mysqli_fetch_assoc($profile_query)) {
        $match_record_row_fetch[] = $fetch_row;
    }
}

$get_umpire_match_dateData = $data->get_umpire_match_dates($search_id);
$dateData  = $get_umpire_match_dateData->fetch_assoc();

$first_match_date = !empty($dateData['first_match_date']) ? date("d/m/Y", strtotime($dateData['first_match_date'])) : '-';
$last_match_date  = !empty($dateData['last_match_date'])  ? date("d/m/Y", strtotime($dateData['last_match_date'])) : '-';
$html = '';
$html .= '<div class="container" id="<?php echo $umpire_id; ?>">
            <div class="body_content">
                <div class="top_paragrap_content">
                    <div class="left_sidbar">
                        <div class="profile_img">';
                            if (!empty($match_record_row_fetch[0]['umpire_image'])){
                                $html .= '<img src="'.$site_url.'/'.$match_record_row_fetch[0]['umpire_image'].'" alt="Umpire Image" style="max-width:200px; max-height:200px; margin:10px 0;">';
                            } else {
                                $html .= '<img src="'.$site_url.'/assets/images/umpire-profile/umpire-default.jpg" alt="Umpire Image" style="max-width:200px; max-height:200px; margin:10px 0;">';
                            }
                        $html .= '</div>';
                     $html .= '</div>';
                    $html .= '<div class="right_box">
                        <div class="header_top">
                            <div class="left_cont">
                                <h2>'.$match_record_row_fetch[0]['umpire_csv_name'].'</h2>
                            </div>
                        </div>
                        <div class="umpire-paragrap">
                            '.$match_record_row_fetch[0]['umpire_description'].'
                        </div>
                    </div>
                </div>
            </div>
            <div class="next_tablae_box">
                <div class="left_sidbar">
                    <div class="profile_content_list">
                         <table id="umpire_profil_table_download" border="1" cellpadding="8" cellspacing="0" width="100%">
                             <thead>
                                 <tr style="border-width:0px;">
                                    <th>Number of matches</th>
                                    <th>First Match Date</th>
                                    <th>Last Match Date</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 <tr>
                                    <td style="text-align: center;">'.$match_record_profile_rowcount.'</td> 
                                    <td style="text-align: center;">'.$first_match_date.'</td> 
                                    <td style="text-align: center;">'.$last_match_date.'</td> 
                                 </tr>

                             </tbody>
                         </table>
                        
                      
                    </div>
                </div>
            </div>
        </div>';
$html .= '<div class="result_filter">
            <div class="left_rusult_pre">
                <p style="font-size: 15px;">'.$rowCount.' Results</p>
            </div>
        </div>

        <table id="umpire_profil_table_download" border="1" cellpadding="8" cellspacing="0" width="100%">
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
        $html .= "<tr>";
        // 1) Match Date
        $html .= "<td style='font-size: 13px; text-align: center;' class='date'>{$formatted_date}</td>";

        // 2) Match Details (home + away)
        $html .= "<td class='team_vs'>
                <div class='match_dtls' style=' margin-bottom: 6px;'>";

        // --- HOME TEAM DETAILS ---
        $team_details = $data->aflua_all_team_details('afl_teams', $matches_record_home_team_id, 1, 1);
        if (mysqli_num_rows($team_details) > 0) {
            $team_details_row = mysqli_fetch_assoc($team_details);
            $home_team_image = $team_details_row['team_color_image'];

            $html .= "<img style='height: 20px;' src='" .$site_url. "" .$home_team_image. "' alt='Home Team'>                
                    <span style='font-size: 13px;' class='team_name'>" . ucwords($matches_record_home_team) . "</span>                  
                      <span style='font-size: 13px;' class='goal'>" .$matches_record_goals1. "</span>.<span style='font-size: 13px;' class='be_goal'>" .$matches_record_behinds1. "</span>
                      (<span style='font-size: 13px;' class='point'>" .$matches_record_points1. "</span>)
                    ";
        }

        $html .= "</div>
              <div class='match_dtls'>";

        // --- AWAY TEAM DETAILS ---
        $team_details = $data->aflua_all_team_details('afl_teams', $matches_record_away_team_id, 1, 1);
        if (mysqli_num_rows($team_details) > 0) {
            $team_details_row = mysqli_fetch_assoc($team_details);
            $away_team_image = $team_details_row['team_color_image'];

            $html .= "<img style='height: 20px;' src='" .$site_url. "" .$away_team_image. "' alt='Away Team'>
            <span  style='padding-right: 25px; font-size: 13px;' class='team_name'>" . ucwords($matches_record_away_team) . "                   
                      <span class='goal'>" .$matches_record_goals2. "</span>.<span class='be_goal'>{$matches_record_behinds2}</span>
                      (<span class='point'>" .$matches_record_points2. "</span>)</span>";
        }

        $html .= "</div></td>";

        // 3) Umpires (keep structure from your original code)

        // $html .= "<td style='font-size: 13px; text-align: center;'>";
        // if (!empty($search_id)) {
        //     $umpire_details = $data->aflua_all_umpire_details_by_id_fetch('afl_umpires', $search_id, 1, 1);
        //     if (mysqli_num_rows($umpire_details) > 0) {
        //         $umpire = mysqli_fetch_assoc($umpire_details);
        //         $html .= ucwords($umpire['umpire_type']);                               
        //     }
        // }
        // $html .= "</td>";


        $html .= "<td style='font-size: 13px; text-align: center;'>";
        if (!empty($search_id)) {
            $html .= ucwords($discipline_html);
        }
        $html .= "</td>";

        // 4) Ground
        $html .= "<td style='font-size: 13px; text-align: center;'>";
        if (!empty($matches_record_venue)) {
            $html .= ucwords($matches_record_venue);
        }
        $html .= "</td>";

        // 5) Competition
        $html .= "<td style='font-size: 13px; text-align: center;'>";
        if (!empty($matches_record_competition)) {
            $html .= "<span class='comp_col " . htmlspecialchars($matches_record_competition) . "'>" . strtoupper($matches_record_competition) . "</span>";
        }
        $html .= "</td>";

        // 6) Round
        $html .= "<td style='font-size: 13px; text-align: center;'>";
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


    // Generate PDF
    $mpdf = new \Mpdf\Mpdf(['format' => [264, 225]]);

    $mpdf->SetHeader("Umpire Profile - ".time()." ");
    $mpdf->SetFooter("© Aflua | Page {PAGENO}");
    $mpdf->WriteHTML($html);
    $mpdf->Output("Career-of-".ucwords($umpire_name)."-".time().".pdf", \Mpdf\Output\Destination::DOWNLOAD);
?>
