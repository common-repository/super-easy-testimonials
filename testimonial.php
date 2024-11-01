<script type="text/javascript">
    function set_showDetails(id)
    {
        if (id)
        {
            document.getElementById('details' + id).style.display = 'block';
        }
    }
    function set_hideDetails(id)
    {
        if (id)
        {
            document.getElementById('details' + id).style.display = 'none';
        }
    }
</script>
<?php
$globalimagepath = wp_upload_dir();
global $globalimagepath;
/* * ************************** Insert into database ******************************** */
if ($_REQUEST['submit']) {
    $table_name = $wpdb->prefix . 'super_easy_testimonials';
    //echo $table_name; exit;
    $testimonial_title = sanitize_text_field($_POST['testimonial_title']);
    $testimonial_description = sanitize_text_field($_POST['testimonial_description']);
    $author_name = sanitize_text_field($_POST['author_name']);
    $author_image = sanitize_file_name($_FILES['fileupload']['name']);
    $author_address = sanitize_text_field($_POST['author_address']);
    $split_name = explode('.', $author_image);
    $time = time();
    if(!empty($author_image))
    {
        $file_name = $time . "." . $split_name[1];
        $path_array = wp_upload_dir();
        $path = $path_array['basedir'] . "/authorimage";
        move_uploaded_file($_FILES["fileupload"]["tmp_name"], $path . "/" . $file_name);
    }
    else
    {
        $file_name = '';
    }
    $date = date('Y-m-d');
    $insertsql = $wpdb->insert($table_name,
                              array('testimonial_title'=>$testimonial_title, 'testimonial_description'=>$testimonial_description, 'author_image'=>$file_name, 'author_name'=>$author_name, 'author_address'=>$author_address, 'created'=>$date),
                              array('%s', '%s', '%s', '%s', '%s', '%s', '%d'));
    //print_r($insertsql); exit;
}
/* * ************************** Insert into database ******************************** */
//Fetching a single record for update purpose
if ($_REQUEST['id'] && $_REQUEST['action'] == 'edit') {
    $table_name = $wpdb->prefix . 'super_easy_testimonials';
    $id = $_REQUEST['id'];
    $fetchsinglerecord = $wpdb->get_row($wpdb->prepare("SELECT * from $table_name where id=%d", $id));
}
// Fetching a single record for update purpose
/* Edit the selected record */
global $wbdb;
if ($_REQUEST['edit'] && $_REQUEST['id']) {
    $table_name = $wpdb->prefix . 'super_easy_testimonials';
    $id = $_REQUEST['id'];
    $testimonial_title = sanitize_text_field($_POST['testimonial_title']);
    $testimonial_description = sanitize_text_field($_POST['testimonial_description']);
    $author_name = sanitize_text_field($_POST['author_name']);
    $author_image = sanitize_file_name($_FILES['fileupload']['name']);
    $author_address = sanitize_text_field($_POST['author_address']);
    $split_name = explode('.', $author_image);
    $time = time();
    $file_name = $time . "." . $split_name[1];
    $dirRoot = site_url();
    if ($author_image) {
        $path_array = wp_upload_dir();
        $path = $path_array['basedir'] . "/authorimage";
        //$path = str_replace('\\', '/', $path_array['path']);
        move_uploaded_file($_FILES["fileupload"]["tmp_name"], $path . "/" . $file_name);
        $updatesql = $wpdb->query($wpdb->prepare("UPDATE $table_name SET `testimonial_title`=%s, `testimonial_description`=%s, `author_image`= %s, `author_name`= %s, `author_address`= %s, `is_active`=%d WHERE id = %d;", $testimonial_title, $testimonial_description, $file_name, $author_name, $author_address, 1, $id));
    } else {
        $updatesql = $wpdb->query($wpdb->prepare("UPDATE $table_name SET `testimonial_title`=%s, `testimonial_description`=%s, `author_name`= %s, `author_address`= %s, `is_active`=%d WHERE id = %d;", $testimonial_title, $testimonial_description, $author_name, $author_address, 1, $id));
    }
}
/* Edit the selected record */

//Delete the selected record

if ($_REQUEST['id'] && $_REQUEST['action'] == 'delete') {
	$table_name = $wpdb->prefix . 'super_easy_testimonials';
    $id = $_REQUEST['id'];
    $selectsql = $wpdb->get_row("SELECT * from $table_name where id=$id");
    if ($selectsql) {
        if ($selectsql->author_image) {
            /* fetched the upload director array */
            $wpUploadDir = wp_upload_dir();
            @unlink($wpUploadDir['basedir'] . '/authorimage' . '/' . $selectsql->author_image);
            $deletesql = $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id=%d", $id));
        } else {
            $deletesql = $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id=%d", $id));
        }
    }
}

//Delete the selected record

/* Run the fetch query in the begining */
$table_name = $wpdb->prefix . 'super_easy_testimonials';
$countersql = $wpdb->get_var("select count(*) from $table_name");
$fetchdatasql = $wpdb->get_results("SELECT * FROM $table_name order by id desc");
/* Fetch query ends here */
?>
<form method="POST" name='create_testimonial' id='create_testimonial' enctype="multipart/form-data">
    <table>
        <tr>
            <td>Title:</td>
            <td><input type="text" name="testimonial_title" value="<?php echo $fetchsinglerecord->testimonial_title; ?>" required></td>
        </tr>
        <tr>
            <td>Description:</td><td><div id="poststuff">
    <?php the_editor($fetchsinglerecord->testimonial_description,'testimonial_description'); ?>
</div></td>
        </tr>
        <tr>
            <td><label>Author Name:</label></td>
            <td><input type="text" name="author_name" value="<?php echo $fetchsinglerecord->author_name; ?>" required></td>
        </tr>
        <tr>
            <td>Author Picture:</td>
            <td>
                <?php if ($fetchsinglerecord->author_image) { ?>
                    <img src="<?php echo $globalimagepath['baseurl'] . '/authorimage/' . $fetchsinglerecord->author_image; ?>" width="75" height="75">
                <?php } ?>
                <input type="file" name="fileupload" size="50" id="file" /></td>
        </tr>
        <tr>
            <td>Author Address:</td>
            <td><textarea name="author_address" rows='4' cols='45'> <?php echo $fetchsinglerecord->author_address; ?></textarea></td>
        </tr>

        <tr>
            <td colspan="2">
                <?php if ($_REQUEST['id'] && $_REQUEST['action'] == 'edit') { ?>
                    <input type="submit" name="edit" value="Edit Testimonial">
                <?php } else { ?>
                    <input type="submit" name="submit" value="Add Testimonial">
                <?php } ?>
            </td>
        </tr>
    </table>
</form>

<table width="100%" cellpadding="5" cellspacing="5">
    <?php
    if ($countersql >= 1) {
        ?>
        <tr>
            <td>SL NO.</td>
            <td>TITLE</td>
            <td>DESCRIPTION</td>
            <td>AUTHOR NAME</td>
            <td>AUTHOR IMAGE</td>
            <td>AUTHOR ADDRESS</td>
            <td>CREATED</td>
            <!--<td>STATUS</td>-->
            <td>ACTIONS</td>
        </tr>
        <?php
        $sl = 1;
        foreach ($fetchdatasql as $sql):
            $strlength = strlen($sql->testimonial_description);
            ?>
            <tr>
                <td><?php echo $sl; ?></td>
                <td><?php echo stripslashes($sql->testimonial_title); ?></td>
                <td><?php
                    if ($strlength > 50) {
                        echo substr($sql->testimonial_description, 0, 50) . "<img src='".plugins_url( 'images/more.png', __FILE__ )."' width='12' height='12' style='margin-lset:5px' onmouseover='set_showDetails($sql->id)' onmouseout='set_hideDetails($sql->id)' />";
                    } else {
                        echo stripslashes($sql->testimonial_description);
                    }
                    ?>
                    <div id="details<?php echo $sql->id; ?>" style="display: none;" class="showAll"><?php echo $sql->testimonial_description; ?></div>
                </td>
                <td><?php echo stripslashes($sql->author_name); ?></td>
                <td> 
                    <?php if($sql->author_image) { ?>
                    <img src="<?php echo $globalimagepath['baseurl'] . '/authorimage/' . $sql->author_image; ?>" width="75" height="75">
                    <?php } else { ?>
                    <img src= "<?php echo plugins_url() . '/super-easy-testimonials/images/photo.jpg' ?>" width="75" height="75">
                    <?php } ?>
                </td>
                <td><?php echo stripslashes($sql->author_address); ?></td>
                <td><?php echo $sql->created; ?></td>
                <td> <a href="?page=super-easy-testimonials/testimonial.php&id=<?php echo $sql->id; ?>&action=edit">Edit</a> | <a href="?page=super-easy-testimonials/testimonial.php&id=<?php echo $sql->id; ?>&action=delete" onclick="return confirm('Are you sure?')">Delete</a> </td>
            </tr>
            <?php
            $sl++;
        endforeach;
    } else {
        ?>
        <tr>
            <td>
                <h3 style="font-style:italic;">Whoops, No Testimonials Found, Add Some Testimonials First!!</h3>
            </td>
        </tr>
        <?php
    }
    ?>
</table>