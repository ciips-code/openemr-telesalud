// Show Teleconsultation button 
function showVCButton(authUserID, pid) {
    $.ajax({
        type: 'GET',
        url: '/telesalud/controllers/C_TSalud_Vc.php',
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
// Function for copy content to cplipboard ß
function copyLinkToClipboard(linkElementId) {

    // Get the link tag
    var linktag = document.getElementById(linkElementId);
    // Copy the text inside the text field
    navigator.clipboard.writeText(linktag.attr("href"));
    // Alert the copied text
    alert("Copied the text: " + linktag.attr("href"));

}