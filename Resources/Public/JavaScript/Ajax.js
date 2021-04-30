document.addEventListener("DOMContentLoaded", function() {

    let slugHelper = new SlugHelper();

    let filter_key = document.getElementById('filter_key');
    let filter_maxentries = document.getElementById('filter_maxentries');
    let filter_orderby = document.getElementById('filter_orderby');
    let filter_order = document.getElementById('filter_order');

    let filter_key_value = '';
    let filter_maxentries_value = filter_maxentries.value;
    let filter_orderby_value = filter_orderby.value;
    let filter_order_value = filter_order.value;

    filter_maxentries.addEventListener('change',function(e){
        filter_maxentries_value = filter_maxentries.value;
        loadList('pages','title','slug',0);
    });

    filter_key.addEventListener('input',function(e){
        filter_key_value = filter_key.value;
        loadList('pages','title','slug',0);
    });

    filter_orderby.addEventListener('change',function(e){
        filter_orderby_value = filter_orderby.value;
        loadList('pages','title','slug',0);
    });

    filter_order.addEventListener('change',function(e){
        filter_order_value = filter_order.value;
        loadList('pages','title','slug',0);
    });

    function loadList(table,titleField,slugField,page){

        let url = TYPO3.settings.ajaxUrls['ajaxList']+'&table='+table+'&page='+page+'&orderby='+filter_orderby_value+'&order='+filter_order_value+'&maxentries='+filter_maxentries_value+'&key='+filter_key_value;
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
                        let recordUid = records[i]['uid'];
                        let sitePrefix = records[i]['sitePrefix'];
                        let fullUrl = sitePrefix + slug;
                        output += '<div id="record-'+recordUid+'" data-siteprefix="'+sitePrefix+'" data-record="'+recordUid+'" class="slug-record row mb-2 border-bottom">';
                        output += '<div class="col py-2">';
                            output += '<div>';
                            output += '<h5 class="slug-title"><i class="fa fa-'+slugHelper.getPageIconByType(records[i]['doktype'],records[i]['is_siteroot'])+' fa-fw"></i> '+title+'</h5>';
                            output += '<a href="'+fullUrl+'" target="_blank" class="slug-preview">'+ fullUrl + '</a>';
                            output += '</div>';
                        output += '</div>';
                        output += '<div class="col py-2">';
                            output += '<div class="input-group"><span class="input-group-addon"></span><input type="text" class="form-control slug-input" value="'+slug+'"/></div>';
                        output += '</div>';
                        output += '<div class="col-sm-2 py-2 d-flex justify-content-end">';
                            output += '<div class="button-group ml-auto"><a class="btn btn-default btn-sm btn-save"><i class="fa fa-save"></i></a><a class="btn btn-default btn-sm btn-generate"><i class="fa fa-refresh"></i></a><a class="btn btn-default btn-sm btn-info"><i class="fa fa-info"></i></a></div>';
                        output += '</div>';
                        output += '<div class="info-container"></div>';
                        output += '</div>';
                    }
                }
                else{
                    top.TYPO3.Notification.error('Ajax Error', slugNotes['notes.error.ajax'] + '' + req.statusText);
                }
            }
            target.innerHTML = output;
            initInterface();
        }
        req.send();
    }

    function initInterface(){
        let rows = document.querySelectorAll('.slug-record');
        rows.forEach((row, i) => {
            let slug_input = row.querySelector('.slug-input');
            let slug_preview = row.querySelector('.slug-preview');
            let button_save = row.querySelector('.btn-save');
            let button_generate = row.querySelector('.btn-generate');
            let button_info = row.querySelector('.btn-info');
            let uid = row.getAttribute('data-record');
            let sitePrefix = row.getAttribute('data-siteprefix');
            button_save.addEventListener('click',function(e){
                let slug = row.querySelector('input[type="text"]').value;
                slugHelper.save(uid,slug,sitePrefix,'page');
            });
            button_generate.addEventListener('click',function(e){
                slugHelper.generate(uid,sitePrefix,'page');
            });
            button_info.addEventListener('click',function(e){
                slugHelper.slugInfo(uid,'page');
            });
            slug_input.addEventListener('input',function(e){
                slug_preview.innerHTML = sitePrefix + slug_input.value;
                slugHelper.updateGooglePreviewUrl(sitePrefix + slug_input.value,uid);
            });
        });
    }

    loadList('pages','title','slug',0);

});
