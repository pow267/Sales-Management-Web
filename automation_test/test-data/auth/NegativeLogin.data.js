function negativeData(){
    return [
        { case: 'sai username', username:'admind', password:'123', success: false},
        { case: 'sai password', username:'admin', password:'1234', success: false},
        { case: 'sai cả hai', username:'admind', password:'1235', success: false},
    ];
}
module.exports = { negativeData };