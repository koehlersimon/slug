document.addEventListener("DOMContentLoaded", function() {

    function loadList(table,titleField,slugField){
        let url = TYPO3.settings.ajaxUrls['ajaxList']+'&table='+table;
        let req = new XMLHttpRequest();
        req.open("GET", url, true);
        req.setRequestHeader("Content-type", "application/json; charset=utf-8");
        req.onreadystatechange = function() {
            if(req.readyState == 4 && req.status == 200) {
                let records = JSON.parse(req.responseText);
                let target = document.getElementById('slug-list-wrap');
                var output = '';
                for (var i = 0; i < records.length; i++) {
                    let title = records[i][titleField];
                    let slug = records[i][slugField];
                    output += '<div class="entry"><div class="title"><strong>' + title +'</strong><br>' + slug + '</div></div>';
                    console.log(title);
                }
                target.innerHTML = output;
            }
            else{
                top.TYPO3.Notification.error('Ajax Error', slugNotes['notes.error.ajax'] + '' + response.statusText);
            }
        }
        req.send();
    }

    loadList('pages','title','slug');

});
