function finssn_waitForMarionette(){
    if(typeof(Marionette) == "undefined") {
        console.log("Marionette undefined")
        setTimeout(finssn_waitForMarionette, 250);
    }
    else{
        console.log("Marionette defined")
        Initialize();
    }
}

finssn_waitForMarionette();

const t_LookupTable = [
    0,
    1,
    2,
    3,
    4,
    5,
    6,
    7,
    8,
    9,
    'A',
    'B',
    'C',
    'D',
    'E',
    'F',
    'H',
    'J',
    'K',
    'L',
    'M',
    'N',
    'P',
    'R',
    'S',
    'T',
    'U',
    'V',
    'W',
    'X',
    'Y'
]

function finssn_getT(val) {
    const key = Number(val)%31
    console.log(key - 1)
    const t = t_LookupTable[key]
    console.log(t)
    return t
}

function finssn_IsValid(val) {
    if (val.length != 11) {return false}
    const y = val[6]
    const t = val[10]
    const birthdate = val.substring(0, 6)
    const nnn = val.substring(7,10)

    function finssn_yValidity() {
        const birthyear = birthdate.substring(4, 6)
        if (birthyear > 1999) {
            if (y == 'A') {
                return true;
            }
        } else {
            if (y == '-' || y == '−') {
                return true;
            }
        }
    }
    return finssn_yValidity() && t == finssn_getT(birthdate+nnn)
}

function Initialize() {
    var myCustomFieldController = Marionette.Object.extend({
        initialize: function() {
            console.log("Initializing")
            // On the Form Submission's field validaiton…
            const submitChannel = Backbone.Radio.channel( 'submit' )
            this.listenTo( submitChannel, 'validate:field', this.validate )
    
            // on the Field's model value change…
            const fieldsChannel = Backbone.Radio.channel( 'fields' )
            this.listenTo( fieldsChannel, 'change:modelValue', this.validate )
        },
        
        validate: function( model ) {
            if (model.get('type') != 'Finnish SSN') {
                return;
            }
            const fieldId = model.get('id')
            const errorMsg = 'Please enter a valid SSN'
            const errorId = 'Invalid-FIN-SSN'
            const fieldValue = String(model.get('value'))
            if (finssn_IsValid(fieldValue)) 
            {
                Backbone.Radio.channel( 'fields' ).request( 'remove:error', fieldId, errorId, errorMsg)
            } else {
                Backbone.Radio.channel( 'fields' ).request( 'add:error', fieldId, errorId, errorMsg)
            }
        }
    });
    new myCustomFieldController();
}