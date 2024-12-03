import { Model as BaseModel } from 'vue-api-query';


export default class Model extends BaseModel {

  // define a base url for a REST API
  baseURL () {
    return process.env.MIX_VUE_APP_ROOT_API;
  }


  /* api query requesting hook */
  request (config) {
//console.log('requesting..');
//console.log(config);
    return this.$http.request(config);
  }
  
  
  /* Inject defaults to model */
  withDefaults(schema,require=true){
    
    if(!schema) return this;
    else if(!schema.model) return this;

    for(let key in schema.model)                                                // if this[key] isnt set, then [reactively] assign the default davlue from schema
      if((typeof(this[key]) === 'undefined' || this[key]===null))
        this[key] = (typeof schema.model[key] === 'object' && schema.model[key]!==null && !Array.isArray(schema.model[key])) ? Object.assign({}, schema.model[key]) : schema.model[key];
      else if(typeof(this[key]) === 'object')                                   // defaults for a nested object in model defaults.
        for(let skey in schema.model[key])
          if((typeof(this[key][skey]) === 'undefined' || this[key][skey]===null))
            this[key][skey] = schema.model[key][skey];

    if(require)
      for(let def in this)                                                      // remove properties that are not in schema model (defaults) if require is set to true
        if(Object.keys(schema.model).indexOf(def)===-1)
          delete this[def];
  
    return this;
    
  }
  
  
  /* include field and whereIn filters */
  setFilters(filters){
      
      Object.keys(filters).forEach(k => {
        
        let list = filters[k].map((val) => {                                      // parse input data to query strings
          if(val instanceof Date) return val.toISOString();                       // send time in utc string from local input
          else return val;
        });
  
        this.whereIn(k,list);                                                     // include in whereIn
  
      });
      
      
      return this;
      
  }
  
  
  /* get a url string */
  getUrl() {

    let base = this._fromResource || `${this.baseURL()}/${this.resource()}`;
    base = this._customResource ? `${this.baseURL()}/${this._customResource}` : base;
    return `${base}${this._builder.query()}`;

  }
  


  /* TODO api query update response hook */

  
  
  
  
}