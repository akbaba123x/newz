//calculates which copy settings should be visible
function calcSettings() {
    var data = []; //array of GroupData encoded data from 'data-printer' attributes
    data.push($('#sourceSelect option:selected').data('printer')); //add data of selected source printer to data array.
    var targetType = $('#targetType :selected').val();
    var considerTargets = $('#matchingTargetRadio:checked').length == 1;
    if (considerTargets) { //check if targets should be considered or only source
        //add data from all targets in target list
        var selectedTargets = $('#targetList #printer-list input:checked');
        if (selectedTargets != null) {
            for (var i=0; i < selectedTargets.length; i++) {
                data.push($(selectedTargets[i]).data('printer'));
            }
        }
        
        //add data from all group in target group list
        var selectedGroups = $('#groupList #printer-group-list input:checked');
        if (selectedGroups != null) {
            for (var i=0; i < selectedGroups.length; i++) {
                data.push($(selectedGroups[i]).data('printer'));
            }
        }
    }
    
    var printers = 0;
    var devices = 0;
    var types = {};
    var accountConfirm = true;
    var funcIntersect = {'COPIER': 'COPIER', 'SCANNER': 'SCANNER', 'FAX': 'FAX', 'RELEASE': 'RELEASE'};
    for (var i=0; i < data.length; i++) {
        var groupData = data[i];
        for (var j=0; j < groupData.types.length; j++) {
            var type = groupData.types[j];
            types[type] = type; //add type to types map
            
            //count number of printers and devices
            if (type.indexOf('EXT_') == 0) {
                devices++;
            } else {
                printers++;
            }
        }
        
        //compute intersection of current functions with this group data functions
        var newFuncIntersect = {};
        for (var j=0; j < groupData.funcs.length; j++) {
            var func = groupData.funcs[j];
            if (funcIntersect[func] != null) {
                newFuncIntersect[func] = func;
            }
        }
        
        funcIntersect = newFuncIntersect; //update current functions intersection
        accountConfirm = accountConfirm && groupData.accountConfirm; //update account confirmation indicator
    }
    
    var devicesSameType = false;
    var printerSettingsId = '#printerSettings';
    if (shouldHidePrinters(devices, printers, targetType, considerTargets)) {
        //there are devices, so we do not show printer settings
        hideAndUncheck(printerSettingsId);
        
        //count how many types are in types map
        var numTypes = 0;
        for (var key in types) {
            numTypes++;
        }
        
        //if there is only 1 type then the selected devices must be of the same type
        if (numTypes == 1) {
            devicesSameType = true;
        }
    } else {
        //there are no devices, show printer settings
        $(printerSettingsId).show();
    }
    
    var deviceSettingsId = '#deviceSettings'; 
    if (shouldHideDevices(devices, printers, targetType, considerTargets)) {
        //there are printers, do not show device settings
        hideAndUncheck(deviceSettingsId);
    } else {
        $(deviceSettingsId).show(); //there are not printers, show device settings
        
        //show same device setting if devices are of same device
        var deviceSpecificDivId = '#DEVICE_SPECIFIC'; 
        if (devicesSameType) {
            $(deviceSpecificDivId).show();
        } else {
            hideAndUncheck(deviceSpecificDivId);
        }
    }
    
    var accountConfirmId = '#ACCOUNT_CONFIRM'; 
    if (accountConfirm) {
        $(accountConfirmId).show();
    } else {
        hideAndUncheck(accountConfirmId);
    }
    
    var copierId = '#COPIER';
    if (funcIntersect['COPIER'] == null) {
        hideAndUncheck(copierId);
    } else {
        $(copierId).show();
    }
    
    var scannerId = '#SCANNER';
    if (funcIntersect['SCANNER'] == null) {
        hideAndUncheck(scannerId);
    } else {
        $(scannerId).show();
    }
    
    var faxId = '#FAX';
    if (funcIntersect['FAX'] == null) {
        hideAndUncheck(faxId);
    } else {
        $(faxId).show();
    }
    
    var releaseId = '#RELEASE_DEVICE';
    if (funcIntersect['RELEASE'] == null) {
        hideAndUncheck(releaseId);
    } else {
        $(releaseId).show();
    }
}

//hides element with passed id and unchecks all child input elements
function hideAndUncheck(id) {
    $(id).hide();
    $(id + ' input').prop('checked', false);
}

function shouldHidePrinters(numDevices, numPrinters, targetType, considerTargets) {
    if (numDevices > 0) {
        return true; //hide if there is at least one device
    }
    
    //hide if there are no devices, only one printer (means that no target was selected), targets are considered and
    //the target type selected option is not 'PRINTERS'
    if (considerTargets && numPrinters == 1 && targetType != 'PRINTERS') {
        return true;
    }
    
    return false;
}

function shouldHideDevices(numDevices, numPrinters, targetType, considerTargets) {
    if (numPrinters > 0) {
        return true; //hide if there is at least one printer
    }
    
    //hide if there are no printers, only one device (means that no target was selected), targets are considered and
    //the target type selected option is not 'DEVICES' or 'DEVICES_SAME_TYPE'
    if (considerTargets && numDevices == 1 && targetType != 'DEVICES' && targetType != 'DEVICES_SAME_TYPE') {
        return true;
    }
    
    return false;
}







