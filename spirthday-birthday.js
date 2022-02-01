new Vue({

    el: 'main',

    data: {
        sortKey: '',
        reverse: false,
        sortOrder: '',
        search: '',
        today: '',
        users: [],
        birthdayList: [],
        spirthdayList: [],
        recipientList: [],
    },

    mounted() {
        axios.get("INSERT_URL").then(response => this.users = response.data);
        this.getToday;
    },

    computed: {

        sortedAndFiltered: function() {
            var filtered = this.users.filter((user) => {  
                var dataTerms = user.Name + user.Birthday + user.Baptism;
                var searchTerms = this.search.toLowerCase();
                return dataTerms.toString().toLowerCase().includes(searchTerms);
            });
            
            if (this.reverse) {
                this.sortOrder = 'desc';
            } else {
                this.sortOrder = 'asc';
            }
            return _.orderBy(filtered, this.sortKey, this.sortOrder);
        },
        getToday: function() {
            this.today = new Date();
            //var dd = String(this.today.getDate()).padStart(2, '0');
            var d = String(this.today.getDate());
            //var mm = String(this.today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var m = String(this.today.getMonth() + 1); // January is 0!
            this.today = m + '/' + d;

            //this.today = '2/21';
            return this.today;
        },
        isBirthday: function() {
            this.users.forEach((user) => {
                if (user.Birthday == this.today) {
                    this.birthdayList.push({
                        name: user.Name,
                        email: user.Email,
                        phone: user.Phone
                    });
                }
            });
            if (this.birthdayList === undefined || this.birthdayList.length == 0) {
                // array empty or does not exist
                return false;
            }
            return this.birthdayList;
        },
        isSpirthday: function() {
            this.users.forEach((user) => {
                // Need to trim '/YY' off baptism value to compare with this.today
                if (user.Baptism != null) {
                    user.Baptism = user.Baptism.substr(0, user.Baptism.lastIndexOf("/"));
                }
                if (user.Baptism === this.today) {
                    this.spirthdayList.push({
                        name: user.Name,
                        email: user.Email,
                        phone: user.Phone
                    });
                }
            });
            if (this.spirthdayList === undefined || this.spirthdayList.length == 0) {
                // array empty or does not exist
                return false;
            }
            return this.spirthdayList;
        },
        filterRecipients: function() {
            // check if any spirthdays or birthdays today
            if (this.isSpirthday || this.isBirthday) {
                    
                // combine spirthday and birthday list
                comboList = [];
                comboList = this.spirthdayList.concat(this.birthdayList);

                // create a list of all email recipients
                this.users.forEach((user) => {
                    this.recipientList.push(user.Email);
                });
                
                // remove each spirthday/birthday person from the list of email recipients
                comboList.forEach((combo) => {
                    this.recipientList = this.recipientList.filter(recipient => recipient !== combo.email);
                });
            }
            return this.recipientList;
        }
    },

    methods: {
        sortBy: function(sortKey) {
            this.reverse = (this.sortKey == sortKey) ? ! this.reverse : false;
            this.sortKey = sortKey;
        },
        
    }
    
})