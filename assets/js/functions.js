let favorite = (event) => {
    let $event = $(event.currentTarget);
    let source_user_id = $event.data('id');
    let source = $event.data('source');
    $event.fadeOut();

    $.ajax({
        type: 'POST',
        url: 'favorites',
        data: {
            source_user_id,
            source
        },
        success: (data) => {
            if(data.status == 'error') {
                alert('Please try again later, something is not working properly.');
            }

            else if(data.status == 'success') {
                $event.fadeIn().html(data.details.html);
            }
        },
        dataType: 'json'
    });

    event.preventDefault();

}

let is_url = (str) => (str.includes('http://') || str.includes('https://'));

let number_format = (number, decimals, dec_point = '.', thousands_point = ',') => {

    if(number == null || !isFinite(number)) {
        throw new TypeError("number is not valid");
    }

    if(!decimals) {
        let len = number.toString().split('.').length;
        decimals = len > 1 ? len : 0;
    }

    number = parseFloat(number).toFixed(decimals);

    number = number.replace('.', dec_point);

    let splitNum = number.split(dec_point);
    splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_point);
    number = splitNum.join(dec_point);

    return number;
}