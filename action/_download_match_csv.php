<?php 
include('action_object.php');

$site_title_result = $data->web_setting_details('web_settings','1');
$site_title_res = mysqli_fetch_assoc($site_title_result);

$site_url_result = $data->web_setting_details('web_settings','197');
$site_url_res = mysqli_fetch_assoc($site_url_result);

$site_description_result = $data->web_setting_details('web_settings','198');
$site_description_res = mysqli_fetch_assoc($site_description_result);

$site_url = $site_url_res['field_value'];


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

// --- Year ---
if (!empty($_POST['search_value_8'])) {
    $filters['year'] = $_POST['search_value_8'];
}

//----ASC DESC

if (!empty($_POST['date_flow'])) {
    $filters['date_flow'] = $_POST['date_flow'];
}

$result = $data->get_filtered_search_matches_csv($filters);
if (!$result) {
    die('Database query failed: ' . mysqli_error($data->conn));
}

// $count = mysqli_num_rows($result);
// echo $count;


$time = time();

// set headers to force download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Match-list-download-'.$time.'.csv');

// open output stream
$output = fopen('php://output', 'w');


$custom_headers = ['MATCH NO', 'COMPETITION', 'YEAR', 'SEASON', 'ROUND', 'DAY', 'DATE', 'HOMETEAM', 'GOALS1', 'BEHINDS1', 'POINTS1', 'AWAYTEAM', 'GOALS2', 'BEHINDS2', 'POINTS2', 'VENUE', 'Attendance', 'Shirt colour', 'FIELD1', 'FIELD2', 'FIELD3', 'FIELD4', 'BOUND1', 'BOUND2', 'BOUND3', 'BOUND4', 'BOUND5', 'BOUND6', 'GOAL1', 'GOAL2', 'REPLACE'];
fputcsv($output, $custom_headers);

// output all rows
while ($row = mysqli_fetch_assoc($result)) {
    if (!empty($row['matches_record_date'])) {
        $row['matches_record_date'] = date('d/m/Y', strtotime($row['matches_record_date']));
    }
    if (isset($row['matches_record_competition'])) {
        $row['matches_record_competition'] = ucwords(strtolower($row['matches_record_competition']));
    }
    if (isset($row['matches_record_season'])) {
        $row['matches_record_season'] = ucwords(strtolower($row['matches_record_season']));
    }

    if (isset($row['matches_record_day'])) {
        $row['matches_record_day'] = ucwords(strtolower($row['matches_record_day']));
    }

    if (isset($row['matches_record_home_team'])) {
        $row['matches_record_home_team'] = ucwords(strtolower($row['matches_record_home_team']));
    }

    if (isset($row['matches_record_away_team'])) {
        $row['matches_record_away_team'] = ucwords(strtolower($row['matches_record_away_team']));
    }

    if (isset($row['matches_record_venue'])) {
        $row['matches_record_venue'] = ucwords($row['matches_record_venue']);
    }

    if (isset($row['matches_record_shirt_colour'])) {
        $row['matches_record_shirt_colour'] = ucwords(strtolower($row['matches_record_shirt_colour']));
    }

    if (isset($row['matches_record_umpire_field1'])) {
        $row['matches_record_umpire_field1'] = ucwords(strtolower($row['matches_record_umpire_field1']));
    }
    if (isset($row['matches_record_umpire_field2'])) {
        $row['matches_record_umpire_field2'] = ucwords(strtolower($row['matches_record_umpire_field2']));
    }
    if (isset($row['matches_record_umpire_field3'])) {
        $row['matches_record_umpire_field3'] = ucwords(strtolower($row['matches_record_umpire_field3']));
    }
    if (isset($row['matches_record_umpire_field4'])) {
        $row['matches_record_umpire_field4'] = ucwords(strtolower($row['matches_record_umpire_field4']));
    }

    if (isset($row['matches_record_umpire_boundary1'])) {
        $row['matches_record_umpire_boundary1'] = ucwords(strtolower($row['matches_record_umpire_boundary1']));
    }
    if (isset($row['matches_record_umpire_boundary2'])) {
        $row['matches_record_umpire_boundary2'] = ucwords(strtolower($row['matches_record_umpire_boundary2']));
    }
    if (isset($row['matches_record_umpire_boundary3'])) {
        $row['matches_record_umpire_boundary3'] = ucwords(strtolower($row['matches_record_umpire_boundary3']));
    }
    if (isset($row['matches_record_umpire_boundary4'])) {
        $row['matches_record_umpire_boundary4'] = ucwords(strtolower($row['matches_record_umpire_boundary4']));
    }
    if (isset($row['matches_record_umpire_boundary5'])) {
        $row['matches_record_umpire_boundary5'] = ucwords(strtolower($row['matches_record_umpire_boundary5']));
    }

    if (isset($row['matches_record_umpire_boundary6'])) {
        $row['matches_record_umpire_boundary6'] = ucwords(strtolower($row['matches_record_umpire_boundary6']));
    }

    if (isset($row['matches_record_umpire_goal1'])) {
        $row['matches_record_umpire_goal1'] = ucwords(strtolower($row['matches_record_umpire_goal1']));
    }
    if (isset($row['matches_record_umpire_goal2'])) {
        $row['matches_record_umpire_goal2'] = ucwords(strtolower($row['matches_record_umpire_goal2']));
    }

    if (isset($row['matches_record_umpire_replace'])) {
        $row['matches_record_umpire_replace'] = ucwords(strtolower($row['matches_record_umpire_replace']));
    }

    fputcsv($output, $row);
}

fclose($output);
exit;
?>
