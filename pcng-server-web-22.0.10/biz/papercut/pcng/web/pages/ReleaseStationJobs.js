//for each override link in table, set 'onOverrideClick' to be called on click 
function init() {
    $('#overrideAccounts').change(onOverrideAccountsChange);
    var overrideLinks = $('#jobs-table [data-event-id]');
    for (var i=0; i < overrideLinks.length; i++) {
        var overrideLink = overrideLinks[i];
        var eventId = $(overrideLink).data('event-id');
        var eventTime = $(overrideLink).data('event-time');
        var eventUser = $(overrideLink).data('event-user');
        var eventDoc = $(overrideLink).data('event-doc');
        var data = {eventId: eventId, eventTime: eventTime, eventUser: eventUser, eventDoc: eventDoc};
        $(overrideLink).click(data, onOverrideClick); //pass event id to handler
    }
}

function onOverrideClick(eventObject) {
    stopRefreshTimer();
    $('#overrideEventId').val(eventObject.data.eventId); //set the hidden param with passed event id
    $('#overrideEventTime').text(eventObject.data.eventTime);
    $('#overrideEventUser').text(eventObject.data.eventUser);
    $('#overrideEventDoc').text(eventObject.data.eventDoc);
    var buttons = [{text: overridePrint, click: submitModal},
                   {text: overrideCancel, click: closeModal}];
    $('#overrideModal').dialog({modal: true, resizable: false, title: overrideTitle, buttons: buttons, width: 400, 
        close: onOverrideClose}); //open dialog
}

function submitModal() {
    $('#modalSubmitButton').click();
}

function closeModal() {
    $('#overrideModal').dialog('close');
}

function onOverrideClose() {
    $('#overrideAccounts').val('-1'); //reset selected account to blank option
    $('#overrideComment').val(''); //reset comment
    performAutoRefreshIfRequired();
}

//update comment field whenever account selection changes
function onOverrideAccountsChange() {
    var account = $('#overrideAccounts :selected');
    var comment = '';
    if ($(account).val() != -1) {
        var user = $('#overrideEventUser').text();
        comment = overrideComment.replace('{0}', "'" + user + "'").replace('{1}', "'" + $(account).text() + "'");
        comment = comment.replace('{2}', "'" + overridingUser + "'");
    }
    
    $('#overrideComment').val(comment);
}


