<?php
/*
App Title: Manager Fisiere
App Description:
App Size: 1
App Style:
App Icon: folder
*/
?>
<script type="text/javascript">
    
    $(document).ready( function() {
        
        $('#fileManager').gsFileManager({ script: '<?php echo get_template_directory_uri(); ?>/script/fileManager/connectors/GsFileManager.php' });
        
    });
</script>
<div id="fileManager">

</div>