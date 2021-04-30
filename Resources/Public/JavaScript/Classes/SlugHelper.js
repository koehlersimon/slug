class SlugHelper{

    preloader(){
        return '<div class="d-flex justify-content-center mb-4"><span class="icon icon-size-large icon-state-default icon-spin"><span class="icon-markup"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><g fill="#212121"><path d="M8 15c-3.86 0-7-3.141-7-7 0-3.86 3.14-7 7-7 3.859 0 7 3.14 7 7 0 3.859-3.141 7-7 7zM8 3C5.243 3 3 5.243 3 8s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5z" opacity=".3"/><path d="M14 9a1 1 0 0 1-1-1c0-2.757-2.243-5-5-5a1 1 0 0 1 0-2c3.859 0 7 3.14 7 7a1 1 0 0 1-1 1z"/></g></svg></span></span></div>';
    }

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
        if(isroot === 1){
            return 'globe text-primary';
        }
        else{
            switch(doktype) {
                case 3:
                    return 'link';
                break;
                case 199:
                    return 'minus';
                break;
                case 254:
                    return 'folder';
                break;
                case 4:
                    return 'link';
                break;
                default:
                    return 'file-o';
            }
        }
    }

    slugInfo(uid,type){
        let url = TYPO3.settings.ajaxUrls['slugInfo']+'&type='+type+'&uid='+uid;
        let req = new XMLHttpRequest();
        let slugRow = document.getElementById('record-'+uid);
        let infoContainer = slugRow.querySelector('.info-container');

        // Preloader spinner
        infoContainer.innerHTML = this.preloader();

        req.open("GET", url, true);
        req.setRequestHeader("Content-type", "application/json; charset=utf-8");
        req.onreadystatechange = function() {
            if(req.readyState === 4) {
                if(req.status == 200) {
                    if(req.responseText){
                        infoContainer.innerHTML = req.responseText;
                        let closeButton = infoContainer.querySelector('a.close');
                        closeButton.addEventListener('click',function(){
                            document.getElementById('record-'+uid).querySelector('.info-container').innerHTML = '';
                        });
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
