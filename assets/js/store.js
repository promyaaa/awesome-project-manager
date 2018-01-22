var store = {
    dataObject: {
        message: 'Hello!',
        localizedData: {}
    },
    getLocalizeString: function() {
        var vm = this,
            todo,
            data = {
                action : 'fpm-get-local-data'
            };
        return new Promise((resolve) => {
            jQuery.post( fpm.ajaxurl, data, function( resp ) {
                // console.log(resp);
                if ( resp.success ) {
                    resolve(resp);
                } else {
                    
                }
            });
        });
    },

    fetchUsers: function( projectid ) {
        var vm = this,
            todo,
            data = {
                action : 'fpm-get-users',
                nonce : fpm.nonce,
                project_id: projectid
            };

        return new Promise((resolve) => {
            jQuery.post( fpm.ajaxurl, data, function( resp ) {
                if ( resp.success ) {
                    resolve(resp);
                } else {
                    
                }
            });
        });
    },

    // updateLocalUsers: function( type, index, data ) {
    //     switch( type ) {
    //         'create':
    //             break;
    //         'update':
    //             break;
    //         'remove':
    //             break;
    //     }
    // }
    // setLocalizeData (newValue) {
    //     this.dataObject.localizedData = newValue;
    // },
    // getLocalizeData () {
    //     return this.dataObject.localizedData;
    // }
}

export default store;