export default {
    install(Vue, options) {
        Vue.prototype.$announcer = function(resp) {

            if(typeof resp === 'undefined') return false;

            let announce = {
                type: 'info',
                title: 'For Your Information',
                icon: 'fal fa-info-circle'
            };

            if (resp.timeout) announce.timeout=resp.timeout; //allow override which is set in util.js

            switch(resp.status){
                case 200:
                case 202:
                case 204:
                    announce.type = 'success';
                    announce.icon = 'fal fa-check-circle';
                    announce.title = 'Success!' ;                   
                    break;
                case 201:
                    announce.type = 'success';
                    announce.icon = 'fal fa-check-circle';
                    announce.title = 'Created';
                    break;
                case 207:
                    announce.type = 'warning';
                    announce.title = 'Partialy Updated..';
                    announce.icon = 'fal fa-bell';
                    break;
                case 422:
                    announce.type = 'danger';
                    announce.title = 'Incorrect Input';
                    announce.message = 'Please check and correct any red messages by your input fields and try again.';
                    announce.icon = 'far fa-exclamation-triangle';
                    break;                
                case 409:
                    announce.type = 'warning';
                    announce.title = 'Whoops - a Minor Data Hiccup Occurred';
                    announce.icon = 'fal fa-bell';
                    if(resp.data.hasOwnProperty('message'))
                        announce.message = resp.data.message;
                    break;
                case 400:
                case 500:
                    announce.type = 'warning';
                    announce.title = 'Whoops, Something went wrong..';
                    announce.icon = 'fal fa-bell';
                    break;
            }
            
            /* prepare messaging or validation / errors */
            if(!resp.data){
                announce.title = 'Whoops';
                announce.message = 'There was a Backend Misc. Hiccup';
            }else if(this.hasOwnProperty('$validator') && resp.data.hasOwnProperty('errors')){
                this.$validator.errors.clear();
                let fields = Object.keys(resp.data.errors);
                for (let i = 0; i < fields.length; i++) {                       // sync backend errors with frontend validation array
                    let field = fields[i];
                    let mes = resp.data.errors[field].join(', ');
                    this.$validator.errors.add({ field: field, msg: mes });
                }
            }else if(resp.data.hasOwnProperty('message')){
                if([200,202,204,409].indexOf(resp.status)!==-1) announce.message = resp.data.message;
                else announce.message = 'Misc. Issue';
            }else{
                announce.title = 'Whoops';
                announce.message = 'There was a Misc. Hiccup';
            }
            
            
        /* display announcement block and return true */ 
        this.$notify(announce);

        };
    }
    
};