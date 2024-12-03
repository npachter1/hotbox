import Model from './Model'

export default class Task extends Model {
    
  resource(){
      return 'admin/auth/task';
  }
  
  primaryKey(){
      return 'id';
  }

  

    
}