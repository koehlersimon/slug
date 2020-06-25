$(document).ready(function(){

    function loadList(table,titleField,slugField){

        $.ajax({
            url: TYPO3.settings.ajaxUrls['ajaxList'],
            method: 'GET',
            dataType: 'json',
            data: {
                table : table
            },
            success: function(response) {
                if(response === '1'){
                    top.TYPO3.Notification.info('', slugNotes['notes.info.slugexists']);
                }
                i=0;
                response.forEach(function(){
                    var title = response[i][titleField];
                    var slug = response[i][slugField];
                    console.log(response[i]);
                    $('#slug-list-wrap').append('<div class="entry"><div class="title"><strong>' + title +'</strong><br>' + slug + '</div></div>');
                    i++;
                });
            },
            fail: function(response){
                top.TYPO3.Notification.error('Ajax Fail', slugNotes['notes.error.ajax'] + '' + response.statusText);
            },
            error: function(response){
                top.TYPO3.Notification.error('Ajax Error', slugNotes['notes.error.ajax'] + '' + response.statusText);
            }
        });

    }

    loadList('pages','title','slug');

});
