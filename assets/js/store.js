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
                if ( resp.success ) {
                    resolve(resp);
                } else {

                }
            });
        });
    },

    setLocalization: function( action ) {
        var self = this,
            data = {
                action: action
            }
        return new Promise((resolve) => {
            jQuery.get( fpm.ajaxurl, data, function(resp) {
                if ( resp.success ) {
                    resolve(resp.data);
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
}

export default store;