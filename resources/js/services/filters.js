export default {
    install(Vue, options) {

        Vue.mixin({
            filters:{
                
                dollar: function(value,prec=2){
                    if(!value) return parseFloat(0).toFixed(prec);
                    return parseFloat(value).toFixed(prec);
                },
              
                localDate: function(dateStr,format='M/D/YY'){
                  return (dateStr) ? moment.utc(dateStr).local().format(format) : 'n/a';
                },

                formattedLocalDate: function(dateStr,format='MM/DD/YYYY'){
                  return (dateStr) ? moment.utc(dateStr).local().format(format) : 'n/a';
                },
                
                dateHours: function(dateStr,to=null){
                    let start = moment.utc(dateStr);
                    let now = moment.utc((to) ? to : new Date());
                    let duration = moment.duration(now.diff(start));
                    
                    return duration.hours()+"Hrs "+duration.minutes()+"Mins"
                },
                
                capitalize: function(value, options) {
                  options = options || {}
                  var onlyFirstLetter = options.onlyFirstLetter != null ? options.onlyFirstLetter : false
                  if (!value && value !== 0) return ''
                  if(onlyFirstLetter === true) {
                    return value.charAt(0).toUpperCase() + value.slice(1)
                  } else {
                    value = value.toString().toLowerCase().split(' ')
                    return value.map(function(item) {
                      return item.charAt(0).toUpperCase() + item.slice(1)
                    }).join(' ')
                  }
                },
                
                ucwords: function(str){
                    if(!str) return '';
                    return str.toLowerCase().replace(/\b[a-z]/g, function(l) {
                        return l.toUpperCase();
                    });
                },
                
                renderValue: function(val,schema){
                    if(!schema) return val;
                    let data = schema.find(v=>v.id==val);
                    return (data) ? data.title || data.name : val;
                },
                
                nl2br: function(str) {
                    return str ? str.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1 <br /> $2') : '';
                },
                
                yesno: function(val){
                    return (val) ? 'Yes' : 'No';
                },

                percentage: function(val){
                    return parseFloat(val).toFixed(0)+' %';
                },

                trimQuantity: function(val) {
                    // assumes number string, trim of trailing zeroes in decimal.
                    // useful to format count/ea whole number inventory, e.g., '34.0000' to '34'
                    if (!isNaN(parseInt(val,10)) && (val*1===parseInt(val,10))) return parseInt(val,10);
                    return val;
                },

                pluralize: function(word, amount) {
                    return (amount > 1 || amount === 0) ? `${word}s` : word;
                }

            },
            methods:{
                inSet(needle,haystack){
                    return (haystack.indexOf(needle) === -1) ? false : true;
                },
                
                sorter(list,desc,field){
                    let dir = (desc===true) ? -1 : 1;
                   
                    function _toString(value) {
                          if (!value) return '';
                          else if (value instanceof Object)
                            return keys(value)
                              .sort()
                              .map(key => toString(value[key]))
                              .join(' ');
                          return String(value);
                    }

                    function _getProperty(o,path){
                      if (path.indexOf('.')) {
                        let subs = path.split(".");
                        let ret = o;
                        for (var i = 0; i < subs.length; i++)
                          ret = ret[subs[i]];
                        return ret;
                      } else return o[path];                    
                    }

                    return list.sort(function(a,b){

                        if (typeof _getProperty(a,field) === 'number' && typeof _getProperty(b,field) === 'number')
                            return _getProperty(a,field) < _getProperty(b,field) ? -1*dir : _getProperty(a,field) > _getProperty(b,field) ? 1*dir : 0;
                        else
                            return _toString(_getProperty(a,field)).localeCompare(_toString(_getProperty(b,field)), undefined, { // Stringify the field data and use String.localeCompare
                                numeric: true
                            })*dir;
                    });
                },
                
                async requirePin(title='Please Confirm Modification'){          // returns true if an inputted pin is confirmed, false if error or cancel
                    const { value: password } = await this.$swal.fire({
                        title: title,
                        html:
                            '<style>.swal2-input {-webkit-text-security: disc;}</style><input id="pin-input0" class="swal2-input swal2-pin" type="text" maxlength="1" tabindex="1" autocomplete="off" value="" autofocus>' +
                            '<input id="pin-input1" class="swal2-input swal2-pin-pad" type="text" maxlength="1" tabindex="2" autocomplete="off" value="">' +
                            '<input id="pin-input2" class="swal2-input swal2-pin-pad" type="text" maxlength="1" tabindex="3" autocomplete="off" value="">' +
                            '<input id="pin-input3" class="swal2-input swal2-pin-pad" type="text" maxlength="1" tabindex="4" autocomplete="off" value="">',
                        focusConfirm: false,
                        inputPlaceholder: 'Enter your AdminPin',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Proceed',
                        onOpen: () => {
                            const inputs = Array.from(document.getElementById('swal2-content').getElementsByClassName('swal2-input'));
                            inputs.forEach((el, i) => {
                                const totalInputs = inputs.length;
                              el.onkeypress = (e) => {
                                if (isFinite(e.key)) {
                                  document.getElementById(`pin-input${i}`).value=e.key; //make sure value gets set before focus lost
                                  if (i+1<totalInputs) {
                                    document.getElementById(`pin-input${i+1}`).focus();
                                  } else {
                                    document.getElementsByClassName('swal2-confirm')[0].focus();
                                  }
                                }
                              };
                                el.onkeydown = (e) => {
                                    if (e.key === 'Backspace') {
                                        const i = parseInt(e.target.id.slice(-1));
                                        const currentFieldValue = e.target.value;
                                        if ((i > 0 && i <= 3) && (currentFieldValue === '')) {
                                            //current input is empty, user probably wants to move to prev input
                                            document.getElementById(`pin-input${i-1}`).focus();
                                            e.preventDefault();
                                        }
                                    } else {
                                        if (!isFinite(e.key)) e.preventDefault(); //numeric only
                                    }
                                };
                            });
                        },
                        preConfirm: () => {
                            const inputs = Array.from(document.getElementById('swal2-content').getElementsByClassName('swal2-input'));
                            return inputs.map(e => {
                                return e.value;
                            }).join('');
                        }
                    });

                    if (password) {
                        return await axios.post('/api/v1/admin/auth/pin',{location_id:this.$store.state.disp.location,pin:password}).then(response =>  {
                            return true;                                        // this will allow the parnet component to proceed to dave data.. 
                        }).catch(error => {
                            this.$swal.fire('Incorrect PinCode.  Try Again?');
                            return false;
                        });
                    }else return false;
                },

                getProductImageUrl: function (item) {
                  if(!(item || {}).product) return '/images/logo.png';
                  else if((item.product.public_img)) return item.product.public_img;
                  else if((item.product.category || {}).public_img) return item.product.category.public_img;
                  else return '/images/logo.png';
                },
                
                renderColumnHeader(key){
                    return `head(${key})`;                                      // simple string interpolation to dynamically call a head/foot slot in b-table grids
                },
                renderColumnFooter(key){
                    return `foot(${key})`;
                },
   
                downloadFile(res){
                      const url = window.URL.createObjectURL(new Blob([res.data],{type: res.headers['content-type'] || 'text/plain' }));
                      const link = document.createElement('a');
                      const name = res.headers.file_name || 'file'+typ;
                      link.href = url;
                      link.setAttribute('download', name);
                      document.body.appendChild(link);
                      link.click();
                      window.URL.revokeObjectURL(url);
                }
            }
        });
    
    }
    
};