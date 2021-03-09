document.addEventListener("DOMContentLoaded", function() {

    function loadList(table,titleField,slugField,page){
        let url = TYPO3.settings.ajaxUrls['ajaxList']+'&table='+table+'&page='+page;
        let req = new XMLHttpRequest();
        let target = document.getElementById('slug-list-wrap');
        let output = '';
        req.open("GET", url, true);
        req.setRequestHeader("Content-type", "application/json; charset=utf-8");
        req.onreadystatechange = function() {
            if(req.readyState === 4) {
                if(req.status == 200) {
                    let records = JSON.parse(req.responseText);
                    for (var i = 0; i < records.length; i++) {
                        let title = records[i][titleField];
                        let slug = records[i][slugField];
                        output += '<div class="entry"><div class="title"><strong>' + title +'</strong><br>' + slug + '</div></div>';
                        console.log(page);
                    }
                }
                else{
                    top.TYPO3.Notification.error('Ajax Error', slugNotes['notes.error.ajax'] + '' + req.statusText);
                }
            }
            target.innerHTML = output;
        }
        req.send();
    }

    loadList('pages','title','slug',0);

});
