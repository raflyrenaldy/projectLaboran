export default class Gate{

    constructor(user){
        this.user = user;
    }

    isAdmin(){
        return this.user.type === 'admin';
    }

    isKeuangan(){
        return this.user.type === 'keuangan';
    }

    isInventory(){
        return this.user.type === 'inventory';
    }

    isUser(){
        return this.user.type === 'user';
    }

    isAdminOrUser(){
        if(this.user.type === 'user' || this.user.type === 'admin'){
            return true;
        }

    }




}