import Model from './Model'

export default class Room extends Model {
    
    resource(){
        return 'admin/grow/rooms';
    }
  
    primaryKey(){
        return 'id';
    }
    
}