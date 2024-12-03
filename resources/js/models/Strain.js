import Model from './Model'

export default class Strain extends Model {
    
    resource(){
        return 'admin/grow/strains';
    }
  
    primaryKey(){
        return 'id';
    }
    
}