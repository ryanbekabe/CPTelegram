<?php
//continue only if $_POST is set and it is a Ajax request
if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){

    include("functions.php");  //include config file
    $db= new Database();
    $user = new Users();
    $item_per_page=10;
    //Get page number from Ajax POST
    if(isset($_POST["page"])){
        $page_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
        if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
    }else{
        $page_number = 1; //if there's no page number, set it to 1
    }

    //get total number of records from database for pagination
    $get_total_rows = $user->GetUsersCount(); //hold total records in variable
    //break records into pages
    $total_pages = ceil($get_total_rows/$item_per_page);
    //get starting position to fetch the records
    $page_position = (($page_number-1) * $item_per_page);
    //Limit our results within a specified range.
    $results = $db->query("SELECT * FROM users ORDER BY id DESC LIMIT $page_position,$item_per_page");
    $db->execute(); //Execute prepared Query
    $re = $db->resultset();
    //Display records fetched from database.
    ?>
    <table class="table table-striped border-top" id="sample_1">
        <thead>
        <tr>
            <th>#</th>
            <th class="hidden-phone">نام کاربری</th>
            <th class="">عضو</th>
            <th class="hidden-phone">تاریخ عضویت</th>
            <th class="">شماره عضویت</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($re as $row){ //fetch values
            if($row['sub'] == 1)
                $f = "بلی";
            else
                $f = "خیر";
            ?>
            <tr class="odd gradeX">
                <td><?php echo $row['id'];?></td>
                <td class="hidden-phone"><?php echo $row['username'];?></td>
                <td class=""><span class="label label-success"><?php echo $f;?></span></td>
                <td class="hidden-phone"><?php echo date("h:i:s - Y/m/d",$row['regdate']);?></td>
                <td class=""><a href="users.php?id=<?php echo $row['user_id'];?>" target="_blank"><?php echo $row['user_id'];?></a> </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <?php
    echo '<div align="center">';
    /* We call the pagination function here to generate Pagination link for us.
    As you can see I have passed several parameters to the function. */
    echo paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages);
    echo '</div>';
    exit;
}
################ pagination function #########################################
function paginate_function($item_per_page, $current_page, $total_records, $total_pages)
{
    $pagination = '';
    if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ //verify total pages and current page number
        $pagination .= '<ul class="pagination pagination-lg">';

        $right_links    = $current_page + 3;
        $previous       = $current_page - 3; //previous link 
        $next           = $current_page + 1; //next link
        $first_link     = true; //boolean var to decide our first link

        if($current_page > 1){
            $previous_link = ($previous==0)? 1: $previous;
            $pagination .= '<li class=""><a href="#" data-page="1" title="اولین">&laquo;</a></li>'; //first link
            $pagination .= '<li><a href="#" data-page="'.$previous_link.'" title="قبلی">&lt;</a></li>'; //previous link
            for($i = ($current_page-2); $i < $current_page; $i++){
                //Create left-hand side links
                if($i > 0){
                    $pagination .= '<li><a href="#" data-page="'.$i.'" title="صفحه'.$i.'">'.$i.'</a></li>';
                }
            }
            $first_link = false; //set first link to false
        }
        for($i = $current_page+1; $i < $right_links ; $i++){ //create right-hand side links
            if($i<=$total_pages){
                $pagination .= '<li><a href="#" data-page="'.$i.'" title="صفحه '.$i.'">'.$i.'</a></li>';
            }
        }
        if($current_page < $total_pages){
            $next_link = ($i > $total_pages) ? $total_pages : $i;
            $pagination .= '<li><a href="#" data-page="'.$next_link.'" title="بعدی">&gt;</a></li>'; //next link
            $pagination .= '<li class=""><a href="#" data-page="'.$total_pages.'" title="آخرین">&raquo;</a></li>'; //last link
        }

        $pagination .= '</ul>';
    }
    return $pagination; //return pagination links
}

?>

