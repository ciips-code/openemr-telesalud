// Show Videoconsultation button 
function showVCButton(authUserID, pid) {
    $.ajax({
        type: 'GET',
        url: '/telehealth/controllers/C_TSalud_Vc.php',
        data: {
            action: 'vcButton',
            pc_aid: authUserID,
            pc_pid: pid,
        },
        //dataType: 'json',
        success: function (data) {
            // replace the contents of the div with the link teleconsultation 
            $('#vcButton').html(data);

        }
    });
}