class SlugHelper{

    save(uid,slug,sitePrefix,type){
        let url = TYPO3.settings.ajaxUrls['savePageSlug']+'&slug='+slug+'&uid='+uid;
        let req = new XMLHttpRequest();
        let slugRow = document.getElementById('record-'+uid);
        let slugInputField = slugRow.querySelector('.slug-input');
        let slugPreview = slugRow.querySelector('.slug-preview');
        slugInputField.setAttribute('disabled','disabled');

        req.open("GET", url, true);
        req.setRequestHeader("Content-type", "application/json; charset=utf-8");
        req.onreadystatechange = function() {
            if(req.readyState === 4) {
                if(req.status == 200) {
                    let response = JSON.parse(req.responseText);
                    if(response.status === '1'){
                        top.TYPO3.Notification.success(slugNotes['notes.success.saved'], response.slug);
                        slugRow.classList.remove('not-saved');
                        slugPreview.setAttribute('href',sitePrefix + response.slug);
                    }
                    else{
                        top.TYPO3.Notification.info(slugNotes['notes.info.nochanges'], response.slug);
                    }
                    slugInputField.removeAttribute('disabled');
                }
                else{
                    top.TYPO3.Notification.error('Ajax Error', slugNotes['notes.error.ajax'] + '' + req.statusText);
                }
            }
        }
        req.send();
    }

    generate(uid,sitePrefix,type){
        let url = TYPO3.settings.ajaxUrls['generatePageSlug']+'&uid='+uid;
        let req = new XMLHttpRequest();
        let slugRow = document.getElementById('record-'+uid);
        let slugInputField = slugRow.querySelector('.slug-input');
        let slugPreview = slugRow.querySelector('.slug-preview');
        slugInputField.setAttribute('disabled','disabled');

        req.open("GET", url, true);
        req.setRequestHeader("Content-type", "application/json; charset=utf-8");
        req.onreadystatechange = function() {
            if(req.readyState === 4) {
                if(req.status == 200) {
                    let response = JSON.parse(req.responseText);
                    console.log(response);
                    if(response.slug !== slugInputField.value){
                        top.TYPO3.Notification.success(slugNotes['notes.success.generated'], response.slug);
                        slugRow.classList.add('not-saved');
                    }
                    else{
                        top.TYPO3.Notification.info(slugNotes['notes.info.nochanges'], response.slug);
                    }
                    slugInputField.removeAttribute('disabled');
                    slugInputField.value = response.slug;
                    slugPreview.innerHTML = sitePrefix + response.slug;
                }
                else{
                    top.TYPO3.Notification.error('Ajax Error', slugNotes['notes.error.ajax'] + '' + req.statusText);
                }
            }
        }
        req.send();
    }

    getPageIconByType(doktype,isroot){
        //console.log("doktype: "+doktype+' isroot: '+isroot);
        if(isroot === 1){
            return 'globe text-primary';
        }
        else{
            switch(doktype) {
              case 199:
                return 'minus';
                break;
              case 254:
                return 'file';
                break;
              case 4:
                return 'link';
                break;
              default:
                return 'file';
            }
        }
    }

    slugInfo(uid,type){
        let url = TYPO3.settings.ajaxUrls['slugInfo']+'&type='+type+'&uid='+uid;
        let req = new XMLHttpRequest();
        let slugRow = document.getElementById('record-'+uid);
        let infoContainer = slugRow.querySelector('.info-container');

        req.open("GET", url, true);
        req.setRequestHeader("Content-type", "application/json; charset=utf-8");
        req.onreadystatechange = function() {
            if(req.readyState === 4) {
                if(req.status == 200) {
                    if(req.responseText){
                        infoContainer.innerHTML = req.responseText;
                    }
                    else{
                        top.TYPO3.Notification.info(slugNotes['notes.info.nochanges'], response.slug);
                    }
                }
                else{
                    top.TYPO3.Notification.error('Ajax Error', slugNotes['notes.error.ajax'] + '' + req.statusText);
                }
            }
        }
        req.send();
    }

    updateGooglePreviewUrl(url,uid){
        document.querySelector('div[data-googleurl="'+uid+'"]').innerHTML = url;
    }

}
