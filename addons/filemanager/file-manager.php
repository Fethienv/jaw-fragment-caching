<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

global $jaw_fragments_apikey, $allow_delete,
 $allow_upload, $allow_create_folder,
 $allow_direct_link, $allow_show_folders,
 $disallowed_patterns, $hidden_patterns,
 $PASSWORD, $jaw_fragments_apikey;
?>
<div id="request_url" data-fragment-path="<?php echo FRAGMENT_DIR; ?>" 
     data-api-key="<?php echo $jaw_fragments_apikey; ?>" 
     data-req-url="<?php echo plugins_url('jaw-fragment-caching/addons/file-manager/requests.php'); ?>"  
     data-allow-show-folders="<?php echo $allow_show_folders; ?>" 
     data-hidden-patterns="<?php echo $hidden_patterns; ?>" 
     data-allow-delete="<?php echo $allow_delete; ?>"
     data-allow-upload="<?php echo $allow_upload; ?>"
     data-allow-create-folder="<?php echo $allow_create_folder; ?>"
     data-allow-direct-link="<?php echo $allow_direct_link; ?>"
     data-disallowed-patterns="<?php echo $disallowed_patterns; ?>"
     >
</div>
<div id="top">
    <div class="icon-bar">
        <div class="navbar">
            <a class="active" id='open_fragment_home'><i class="fa fa-fw fa-home"></i> Home</a>
        </div> 
        <div class="jaw_search">
            <label id ='jaw_search_label' for="jaw_search">Search:</label>
            <input type="text" id ='jaw_search'>
        </div>
    </div>
</div>
<div id="top">
<div id="breadcrumb">
    <span>Path:</span>
    <ul class="breadcrumb">
    </ul>
</div>  
</div> 
<div id="top">
   <?php if ($allow_create_folder || $allow_upload): ?> <div id="action_bar"><?php endif; ?>
    <?php if ($allow_create_folder): ?>
        <div id="create_folder">
            <div id="create_folder_target">
            <label id="create_folder_label" for="dirname">Create New Folder</label>
            <input id="dirname" type="text" name="name" value="" />
            <input type="button" id="create_dirname" value="create" />
            </div>
            
        </div>
    <?php endif; ?>
    <?php if ($allow_upload): ?>
        <div id="upload_files">
            <div id="upload_progress"></div>
            <div id="file_drop_target">
                Drag Files Here To Upload
                <b>or</b>
                <input type="file" multiple />
            </div>
        </div>
    <?php endif; ?>
    <?php if ($allow_create_folder || $allow_upload): ?></div><?php endif; ?>
</div> 
<table id="jawc_table"><thead><tr>
            <th>Name</th>
            <th>Size</th>
            <th>Modified</th>
            <th>Permissions</th>
            <th>Actions</th>
        </tr></thead>
    <tbody id="list"></tbody>
    <tbody id="lds-spiner">
        <tr>
            <td colspan="5" style="text-align: center;">
                <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
            </td>
        <tr>
    </tbody>
</table>
<div id="pagination" class="pagination"></div>

