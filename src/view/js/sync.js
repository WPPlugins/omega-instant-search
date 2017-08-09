function runStep(next_step, next_operation, next_entity) {
    var timeout = 5
    var unrecognizedError = 'Unrecoverable Error. Please contact to support';
    if (typeof omegaSyncAjaxUrl == 'undefined') {
        document.getElementById("sync-rows").innerHTML = unrecognizedError;
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        var data;
        var validJson = false;
        try {
            data = JSON.parse(this.response);
            validJson = true
        } catch (e) {
        }
        if (this.readyState == 4 && !validJson) {
            setTimeout(runStep, timeout * 3, next_step, next_operation, next_entity); //try the same next_step again
            document.getElementById("sync-process").innerHTML = this.response;
            return;
        }
        if (this.readyState == 4 && this.status != 200) {
            setTimeout(runStep, timeout * 3, next_step, next_operation, next_entity); //try the same next_step again
            document.getElementById("sync-process").innerHTML = data.html;
            return;
        }
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("sync-process").innerHTML = data.html;
            if (data.next_operation == "finish") {
                document.getElementById("sync-message-block").style.display = 'none';
                document.getElementById("sync-message-block-done").style.display = 'block';
            } else {
                setTimeout(runStep, timeout, data.next_step, data.next_operation, data.next_entity);
            }
        }
    };

    var url = '';
    if (omegaSyncAjaxUrl.search(/\?/) == -1) {
        url = omegaSyncAjaxUrl + '?step=' + next_step;
    } else {
        url = omegaSyncAjaxUrl + '&step=' + next_step;
    }
    url += '&isAjax=1&operation=' + next_operation + '&entity=' +next_entity;

    xhttp.open("POST", url);
    xhttp.send();
}

runStep(1, "init", "init");