function invalidData(){
    return [
        { case: 'username bỏ trống', username: '', password:'123',success:false},
        { case: 'password bỏ trống', username: 'admin', password:'',success:false},
        { case: 'cả hai bỏ trống', username: '', password:'',success:false}
    ];
}
module.exports = { invalidData };

