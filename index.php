<?php

/*! Copyright (c) 2009 Anunay Dahal (http://anunaydahal.com)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * Version: 1.0.0
 *
 */

  
//Loading WP-Config file as this file is calling directly.
if (!function_exists('add_action')) {
  $wp_root = '..';
  if (file_exists($wp_root.'/wp-load.php')) {
    require_once($wp_root.'/wp-load.php');
  } else {
    if (!file_exists($wp_root.'/wp-config.php')) {      
      die("File not found at: $wp_root/wp-config.php");
    }
    require_once($wp_root.'/wp-config.php');
  }
}
  
function getCurrentURL(){
  $protocol = ($_SERVER['HTTPS'] ? 'https://' : 'http://');
  $request_folder = $_SERVER['REQUEST_URI'];
  $request_folder_arr = explode("/",$request_folder);
  $request_folder_path_arr = array();
  for($i=0;$i<count($request_folder_arr)-2;$i++){ 
    // we are skipping last folder as its a folder where this scripts is located, 
    // we don't need this folder to show on New site url 
    $request_folder_path_arr[] = $request_folder_arr[$i];
  }
  $required_request_folder = implode("/",$request_folder_path_arr);
  return $protocol .$_SERVER['HTTP_HOST'] . $required_request_folder;
}
  
function updateWidgetURL($widgets_values,$oldurl,$newurl){
  foreach($widgets_values as $key=>$widget){
    if(is_array($widget and isset($widget))){
      foreach($widget as $k=>$v){
        $widget[$k] = str_replace($oldurl,$newurl,$v);
      }
      $widgets_values[$key] = $widget;
    }
  }
  
  return $widgets_values;
}
  

### Form Processing 
if(!empty($_POST['do'])) {  
  $site_url       = @$_POST['siteurl'];
  $site_title   = @$_POST['sitename'];
  $site_email   = @$_POST['siteemail'];
  
  $companyname  = @$_POST['companyname'];
  $state      = @$_POST['state'];
  
  $old_url    = @$_POST['old_url'];
  $new_url    = $site_url;
  
  $change_password_request = @$_POST['changepass'];
  
  $add_tos_and_privacy = @$_POST['addpptos'];
  
  $findtext = @$_POST['findtext'];
  $replacetext = @$_POST['replacetext'];
  
  
  $queries = array();
  $tbl_options = $wpdb->prefix."options";
  $tbl_posts  = $wpdb->prefix."posts";
  
  $queries[]  = "UPDATE {$tbl_options} SET option_value = '{$new_url}' WHERE option_name = 'home' OR option_name = 'siteurl';";   
  $queries[]  = "UPDATE {$tbl_options} SET option_value = '{$site_title}' WHERE option_name = 'blogname';";   
  $queries[]  = "UPDATE {$tbl_options} SET option_value = '{$site_email}' WHERE option_name = 'admin_email';";    
  $queries[]  = "UPDATE {$tbl_posts} SET guid = replace(guid, '{$old_url}','{$new_url}');";
  $queries[]  = "UPDATE {$tbl_posts} SET post_content = replace(post_content, '{$old_url}', '{$new_url}');";
  $queries[]  = "UPDATE {$tbl_posts} SET post_content = replace(post_content, '{##COMPANYNAME##}', '{$companyname}');";
  $queries[]  = "UPDATE {$tbl_posts} SET post_content = replace(post_content, '{##COMPANYSTATE##}', '{$state}');";
  $queries[]  = "UPDATE {$tbl_posts} SET post_content = replace(post_content, '{##SITETITLE##}', '{$site_title}');";
  $queries[]  = "UPDATE {$tbl_posts} SET post_content = replace(post_content, '{##SITEEMAIL##}', '{$site_email}');";
  
  
  foreach($findtext as $key=>$value){
    if(isset($value) && @$value!="" && strlen($value) > 0){
      $queries[] = "UPDATE {$tbl_posts} SET post_content = replace(post_content, '{$value}', '" . $replacetext[$key] . "');";
    }
  }
  
  
  if(isset($change_password_request) && $change_password_request){
    if(@$_POST['adminpass']!="" && isset($_POST['adminpass'])){
      $userpassword = @$_POST['adminpass'];
      $queries[] = "UPDATE wp_users SET user_pass = MD5( '{$userpassword}' ) WHERE user_login = 'admin';";
    }
  }
    
    
  $terms_of_service = '<h3>1. Terms</h3>
    <p>
      By accessing this web site, you are agreeing to be bound by these 
      web site Terms and Conditions of Use, all applicable laws and regulations, 
      and agree that you are responsible for compliance with any applicable local 
      laws. If you do not agree with any of these terms, you are prohibited from 
      using or accessing this site. The materials contained in this web site are 
      protected by applicable copyright and trade mark law.
    </p>

    <h3>2. Use License</h3>

    <ol type="a">
      <li>
        Permission is granted to temporarily download one copy of the materials 
        (information or software) on {##COMPANYNAME##}\'s web site for personal, 
        non-commercial transitory viewing only. This is the grant of a license, 
        not a transfer of title, and under this license you may not:
        
        <ol type="i">
          <li>modify or copy the materials;</li>
          <li>use the materials for any commercial purpose, or for any public display (commercial or non-commercial);</li>
          <li>attempt to decompile or reverse engineer any software contained on {##COMPANYNAME##}\'s web site;</li>
          <li>remove any copyright or other proprietary notations from the materials; or</li>
          <li>transfer the materials to another person or "mirror" the materials on any other server.</li>
        </ol>
      </li>
      <li>
        This license shall automatically terminate if you violate any of these restrictions and may be terminated by {##COMPANYNAME##} at any time. Upon terminating your viewing of these materials or upon the termination of this license, you must destroy any downloaded materials in your possession whether in electronic or printed format.
      </li>
    </ol>

    <h3>3. Disclaimer</h3>

    <ol type="a">
      <li>The materials on {##COMPANYNAME##}\'s web site are provided "as is". {##COMPANYNAME##} makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties, including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights. Further, {##COMPANYNAME##} does not warrant or make any representations concerning the accuracy, likely results, or reliability of the use of the materials on its Internet web site or otherwise relating to such materials or on any sites linked to this site.</li>
    </ol>

    <h3>4. Limitations</h3>

    <p>In no event shall {##COMPANYNAME##} or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption,) arising out of the use or inability to use the materials on {##COMPANYNAME##}\'s Internet site, even if {##COMPANYNAME##} or a {##COMPANYNAME##} authorized representative has been notified orally or in writing of the possibility of such damage. Because some jurisdictions do not allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you.</p>
        
    <h3>5. Revisions and Errata</h3>

    <p>The materials appearing on {##COMPANYNAME##}\'s web site could include technical, typographical, or photographic errors. {##COMPANYNAME##} does not warrant that any of the materials on its web site are accurate, complete, or current. {##COMPANYNAME##} may make changes to the materials contained on its web site at any time without notice. {##COMPANYNAME##} does not, however, make any commitment to update the materials.</p>

    <h3>6. Links</h3>

    <p>{##COMPANYNAME##} has not reviewed all of the sites linked to its Internet web site and is not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by {##COMPANYNAME##} of the site. Use of any such linked web site is at the user\'s own risk.</p>

    <h3>7. Site Terms of Use Modifications</h3>

    <p>{##COMPANYNAME##} may revise these terms of use for its web site at any time without notice. By using this web site you are agreeing to be bound by the then current version of these Terms and Conditions of Use.</p>

    <h3>8. Governing Law</h3>

    <p>Any claim relating to {##COMPANYNAME##}\'s web site shall be governed by the laws of the State of {##COMPANYSTATE##} without regard to its conflict of law provisions.</p>

    <p> General Terms and Conditions applicable to Use of a Web Site.</p>
  ';

  $terms_of_service = preg_replace(array("/{##COMPANYNAME##}/","/{##COMPANYSTATE##}/"),array($companyname,$state),$terms_of_service);
      
      
  $privacy_policy = '<h2>Privacy Policy</h2>
    <p>Your privacy is very important to us. Accordingly, we have developed this Policy in order for you to understand how we collect, use, communicate and disclose and make use of personal information. The following outlines our privacy policy.</p>
    <ul>
      <li>Before or at the time of collecting personal information, we will identify the purposes for which information is being collected.</li>
      <li>We will collect and use of personal information solely with the objective of fulfilling those purposes specified by us and for other compatible purposes, unless we obtain the consent of the individual concerned or as required by law.</li>
      <li>We will only retain personal information as long as necessary for the fulfillment of those purposes.</li>
      <li>We will collect personal information by lawful and fair means and, where appropriate, with the knowledge or consent of the individual concerned.</li>
      <li>Personal data should be relevant to the purposes for which it is to be used, and, to the extent necessary for those purposes, should be accurate, complete, and up-to-date.</li>
      <li>We will protect personal information by reasonable security safeguards against loss or theft, as well as unauthorized access, disclosure, copying, use or modification.</li>
      <li>We will make readily available to customers information about our policies and practices relating to the management of personal information.</li>
    </ul>
    <p>We are committed to conducting our business in accordance with these principles in order to ensure that the confidentiality of personal information is protected and maintained.</p>';
      
      
      
      
  if($add_tos_and_privacy && isset($add_tos_and_privacy)){
    /* Terms of Service */
    $tos_page = array(
      'post_title' => 'Terms of Service',
      'post_content' => $terms_of_service,
      'post_status' => 'publish',
      'post_date' => date('Y-m-d H:i:s'),
      'post_author' => 1,
      'post_type' => 'page',
      'post_category' => array(0)
    );
    $tos_page_id = wp_insert_post($tos_page);   


    /* Privacy Policy */
    $privacy_policy_page = array(
      'post_title' => 'Privacy Policy',
      'post_content' => $privacy_policy,
      'post_status' => 'publish',
      'post_date' => date('Y-m-d H:i:s'),
      'post_author' => 1,
      'post_type' => 'page',
      'post_category' => array(0)
    );
    $privacy_page_id = wp_insert_post($privacy_policy_page);      
  }

  $query_count = 1;
  $query_error_count = 0;
  $error_query = array();

  foreach($queries as $query){
    if($wpdb->query($query)){
      $query_count++;
    }else{
      //$error_query[$query_error_count++] = $query;
    }
  }

  if($query_error_count > 0 ) {
    $text .= '<p style="color: red;">'.sprintf(__('Error executing queries  \'%s\'.'), implode("<br />",$error_query)).'</p>';
  }else{
    $text .= '<p style="color: green;">'.sprintf(__('Queries executed successfully.'), implode("<br />",$queries)).'</p>';
  } 
} // end form processing.

### Display Form
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Wordpress Path Updater</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="css/view.css" media="all">
<script type="text/javascript" src="scripts/view.js"></script>

<script type="text/javascript">
  jQuery(function($){
    $("#changepass").click(function(){
      if($(this).attr("checked")){
        $("li#li_8").slideDown("fast");
      }else{
        $("li#li_8").slideUp("fast");
      }
    });
    
    $("#sql").click(function(){
      if($(this).attr("checked")){
        $("li#li_11").slideDown("fast");
      }else{
        $("li#li_11").slideUp("fast");
      }
    });
    
    $(".addfields").live("click",function(){
      _dupFields = $(this).parent().clone();
      $(".choices").append(_dupFields);
      $(".frinput:last input").val("");
    });
    
    $(".deletefields").live("click",function(){
      if($(".deletefields").size() > 1){
        $(this).parent().remove();
      }
    });
  });
</script>
</head>
<body id="main_body" >
  <div id="form_container">
    <?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade">'.stripslashes($text).'</div>'; } ?>
    <form id="form_291432" class="appnitro"  method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
      <div class="form_description">
        <h2>Wordpress Settings Updater</h2>
        <p></p>
      </div>            
      <ul>
        <li id="li_6" >
          <label class="description" for="element_6">New Site URL </label>
          <div><input id="siteurl"  name="siteurl" value="<?php echo getCurrentURL();  ?>"  class="element text large" type="text" maxlength="255" /> <br /><small></small></div>
          <p class="guidelines" id="guide_6"><small>Add new wordpress site URL here. <br />Current WP DB Value: <strong> <?php echo get_option('home'); ?></strong></small></p> 
        </li>
        <li id="li_1" >
          <label class="description" for="element_1">New Site Name </label>
          <div><input id="sitename" name="sitename" value="<?php echo get_option('blogname'); ?>" class="element text large" type="text" maxlength="255" value=""/></div>
          <p class="guidelines" id="guide_1"><small>Wordpress site name goes here.<br />Current WP DB Value: <strong><?php echo get_option('blogname'); ?></strong></small></p> 
        </li>
        <li id="li_2" >
          <label class="description" for="element_2">New Site Admin Email </label>
          <div><input id="siteemail" name="siteemail" value="<?php @$_POST['siteemail']; ?>" class="element text large" type="text" maxlength="255" value=""/></div> 
          <p class="guidelines" id="gide_5"><small>Your site admin email goes here. <br />Current WP DB Value: <strong><?php echo get_option('admin_email'); ?></strong></small></p>
        </li>
        
        <li class="section_break">
          <h3 class="no-display">Section Break</h3>
          <p></p>
        </li>
        <li id="li_7" >
          <label class="description" for="element_7">Old Wordpress URL </label>
          <div><input readonly="readonly" id="element_7" name="old_url" value="<?php echo get_option("home");  //(isset($_POST['old_url'])) ? print $_POST['old_url'] : print "http://"; ?>" class="element text large" type="text" maxlength="255" value="http://"/></div>
          <p class="guidelines" id="guide_7"><small>Add old wordpress url here which will repalce by new site URL.<br />Current WP DB Value: <strong><?php echo get_option('home'); ?></strong></small></p> 
        </li>

        <li class="section_break">
          <h3 class="no-display">Section Break</h3>
          <p></p>
        </li>
        <li id="li_5" >
          <label class="description" for="adminpass">Company Name</label>
          <div><input id="companyname" name="companyname" class="element text large" type="text" maxlength="255" value="<?php echo get_option('blogname'); ?>"/></div>
          <p class="guidelines" id="guide_5"><small>Enter your company name here which is used in Privacy Policy and in Terms of Service page.</small></p> 
        </li>
        <li id="li_6" >
          <label class="description" for="adminpass">State</label>
          <div><input id="state" name="state" class="element text large" type="text" maxlength="255" value=""/></div>
          <p class="guidelines" id="guide_5"><small>Enter your State here which is used in Privacy Policy and in Terms of Service page.</small></p>
        </li> 

        <li class="section_break">
          <h3 class="no-display">Section Break</h3>
          <p></p>
        </li>
        <li id="li_5" class="choices" >
          <label class="description" for="adminpass">Find & Replace Database values</label>
          <div class="frlabel"><span class="leftspan">Find</span><span class="rightspan">Replace</span></div>
          <div class="frinput">
            <input id="companyname" name="findtext[]" class="element text soso floatleft right10pxmargin" type="text" maxlength="255" value=""/>
            <input id="state" name="replacetext[]" class="element text soso floatleft" type="text" maxlength="255" value=""/>
            <img class="addfields" title="Add" alt="Add" src="images/add.gif" class="button">
            <img class="deletefields" title="Delete" alt="Delete" src="images/delete.gif" class="button">
          </div>
        </li>   
        <li id="li_6"></li> 
    
        <li class="section_break">
          <h3 class="no-display">Section Break</h3>
          <p></p>
        </li>

        <li id="li_7" >
          <label class="description" for="changepass">Update WP Account settings? </label>
          <span>
            <input id="changepass" name="changepass" class="element checkbox" type="checkbox" value="1" />
            <label class="choice" for="changepass">Yes! I want to update admin password.</label>
          </span> 
        </li>
        <li id="li_8" class="adminpassfield" >
          <label class="description" for="adminpass">Wordpress Admin Password </label>
          <div>
            <input id="adminpass" name="adminpass" class="element text large" type="text" maxlength="255" value=""/> 
          </div>
          <p class="guidelines" id="guide_5"><small>Enter your new wordpress admin password here which will repalce the current one.</small></p> 
        </li>
        <li class="section_break">
          <h3 class="no-display">Section Break</h3>
          <p></p>
        </li>
        <li id="li_9" >
          <label class="description" for="addpptos">Add <u>Privacy Policy</u> & <u>Terms of Service</u> page ? </label>
          <span>
            <input id="addpptos" name="addpptos" class="element checkbox" type="checkbox" value="1" />
            <label class="choice" for="changepass">Yes! Please add these pages :)</label>
          </span> 
        </li>
        <li class="buttons">
          <input id="saveForm" class="button_text" type="submit" name="do" value="<?php _e('Update WP Settings'); ?>"  value="Submit" />
        </li>
      </ul>
    </form> 
    <div id="footer">
      (c) 2009 - <?php echo date("Y"); ?> <a href="http://www.anunaydahal.com" title="Anunay Dahal">Anunay Dahal</a>
    </div>
  </div>
  <img id="bottom" src="images/bottom.png" alt="">
  </body>
</html>