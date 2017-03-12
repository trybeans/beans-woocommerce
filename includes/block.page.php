<?php
    $finishing = 'white';
?>

<div id="beans-section">

    <div id="beans-block-intro" style="display: none;">
        <div class="beans-intro beans-clearfix">
            <a class="beans-cta-button" href="#"  onclick="return Beans.connect();">Join now</a>
            <div class="beans-intro-content">
                Join our rewards program and get <span id="beans-if-join"></span>
                that you can spend now in our shop or save for a later purchase.
            </div>
        </div>
    </div>

    <div id="beans-block-balance" class="box-head" style="display: none;">
        <h2>Balance</h2>
        <div class="beans-intro beans-clearfix">
            <a class="beans-cta-button" href="@card_url">Go to Dashboard</a>
            <div class="beans-intro-content">
                You have <span id="beans-account-balance"></span>
            </div>
        </div>
    </div>

    <div id="beans-block-rules" style="display: none;">
        <h2>Rules</h2>
        <div id="beans-rule-rate" class="beans-rate">
            <span class="beans-beans_rate"></span> are worth <span class="beans-currency_symbol"></span>1
        </div>
        <div id="beans-rule-list" class="beans-rules">
        </div>
    </div>

    <div id="beans-block-history" style="display: none;">
        <h2>History</h2>
        <div id="beans-history-list" class="beans-history">
        </div>
    </div>

    <div id="beans-block-help">
        <h2>Help</h2>
        <h3 class="beans-help-title" id="beans-help-about">What is this?</h3>
        <p>
            This is <span class="beans-company_name"></span> customer rewards program.
            This is our way to say thanks for being part of our journey.
            Our rewards program is designed to let our power customers buy at
            a discounted price or get free products.
        </p>

        <h3 class="beans-help-title" id="beans-help-redemption">How do I spend my <span class="beans-beans_name"></span>?</h3>
        <p>
            <span class="beans-beans_name" style="text-transform: capitalize"></span> can be redeemed directly on
            <a target="_blank" href="@card_website"><span class="beans-website"></span></a>
            during your purchase. The option to redeem your <span class="beans-beans_name"></span> is available on the cart page,
            just before checkout.
            <!--            {% if shop.options.min_beans_redeem %}-->
            <!--            You need to have at least {{ shop.options.min_beans_redeem }} <span class="beans-beans_name"></span>to be able to redeem.-->
            <!--            {% endif %}-->
            <!--            {% if shop.options.range_max_redeem %}-->
            <!--            You can pay up to {{ shop.options.range_max_redeem }}% of your order with your <span class="beans-beans_name"></span>.-->
            <!--            {% endif %}-->
        </p>

        <h3 class="beans-help-title"  id="beans-help-redemption">I am not able to find the redemption button.</h3>
        <p>
            The redemption is not supported if you are browsing with Safari in private browsing
            mode on Mac or iOS (including iPhone, iPod & Pad).
        </p>

        <h3 class="beans-help-title" id="beans-help-inactivity">When do my <span class="beans-beans_name"></span> expire?</h3>
        <p>
            Your <span class="beans-beans_name"></span> have no expiration date.
            <span beans-help="beans:inactivity">
              However you will loose <span beans-rule_beans="beans:inactivity"></span> every
                <span beans-rule_settings="beans:inactivity:period"></span> days for inactivity,
              if you do not get or spend any <span class="beans-beans_name"></span> during this period.
            </span>
        </p>

        <h3 class="beans-help-title" id="beans-help-rules">How do I get more <span class="beans-beans_name"></span>?</h3>
        <p>Please check our list of rules for more information about how to get more <span class="beans-beans_name"></span>.</p>

        <div beans-help="beans:refer_friend_fixed,beans:refer_friend_signup,beans:refer_friend_two_sided,beans:refer_friend_percent">
            <h3 class="beans-help-title" id="beans-help-refer">How do I get <span class="beans-beans_name"></span> for referral?</h3>
            <ol>
                <li>
                    Send your referral link,
                    available <a href="@card_url" target="_blank">here</a>,
                    to all your friends that might be interested.
                </li>
                <li>
                    Make sure they accept your invitation.
          <span beans-help="beans:refer_friend_two_sided">
            Your friend will received an additional <span beans-rule_beans="beans:refer_friend_two_sided"></span>
            when joining with your invitation.
          </span>
          <span beans-help="beans:refer_friend_signup">
            You will get <span beans-rule_beans="beans:refer_friend_signup"></span> when they accept your invitation.
          </span>
                </li>
                <li beans-help="beans:refer_friend_fixed">
                    As soon as they make their first purchase with a minimum value of
                    <span class="beans-currency_symbol"></span><span beans-rule_settings="beans:refer_friend_fixed:minimum"></span>
                    you will get <span beans-rule_beans="beans:refer_friend_fixed"></span>.
                </li>
                <li beans-help="beans:refer_friend_percent"> You will get
                    a <span id="beans_refer_friend_percent_commission"></span>% commission (in <span class="beans-beans_name"></span>)
                    purchase they make.</li>
            </ol>
        </div>

        <div  class="beans-help-block" beans-help="beans:fb_page_like">
            <h3 class="beans-help-title" id="beans-help-fb">How do I get <span class="beans-beans_name"></span> for liking on Facebook?</h3>
            <ol>
                <li>Make sure your Facebook account is connected <a href="@card_url/settings/general/" target="_blank">here</a>.</li>
                <li>Just like our <a href="@card_url/redirect/facebook/" target="_blank">Facebook page</a>.</li>
            </ol>
            <p>
                You should get your <span class="beans-beans_name"></span> within 5 minutes.
                Note that un-liking may result in the cancellation of your credit.
            </p>
        </div>


        <div  class="beans-help-block" beans-help="beans:twitter_follow">
            <h3 class="beans-help-title" id="beans-help-twitter">How do I get <span class="beans-beans_name"></span> for following on Twitter?</h3>
            <ol>
                <li>Make sure your Twitter account is connected <a href="@card_url/settings/general/" target="_blank">here</a>.</li>
                <li>Just like our <a href="@card_url/redirect/twitter/" target="_blank">Twitter page</a>.</li>
            </ol>
            <p>
                You should get your <span class="beans-beans_name"></span> within 5 minutes.
                Note that un-following may result in the cancellation of your credit.
            </p>
        </div>

        <div  class="beans-help-block" beans-help="beans:birthday">
            <h3 class="beans-help-title" id="beans-help-birthday">How do I get <span class="beans-beans_name"></span> for my birthday?</h3>
            <ol>
                <li>Make sure your birthday <a href="@card_url/settings/general/" target="_blank">here</a> is correct.</li>
                <li>Wait until your birthday.</li>
                <li>You will get your credit on your birthday.</li>
            </ol>
        </div>

    </div>

    <div class="beans-cta-bottom-div">
        <a class="beans-cta-button" href="@card_url">Go to rewards dashboard</a>
    </div>

    <script>
        var beans_card;

        var print_beans = function(beans){
            beans = beans == '?' ? '' : Math.round(beans);
            return "<span class='beans-unit' style='color:"+ beans_card.style.secondary_color+";'>" +
                beans + " "+ beans_card.beans_name + "</span>";
        };

        var print_rule = function(rule){
            return  '<div class="beans-rule-icon-circle" style="background-color:'+beans_card.style.primary_color+';"'+
                '> <img class="beans-rule-image" src="'+rule.image.replace('rule/logo/','rule/logo/<?php echo $finishing; ?>/').replace(/\.\w+\.png/,'.png')+'" />'+
                '</div>'+
                '<div class="beans-rule-text-wrapper" class="beans-clearfix">'+
                '  <span class="beans-rule-title" style="color: '+ beans_card.style.primary_color+';">'+
                rule.title+
                '  </span>'+
                '  <span class="beans-rule-description">'+
                rule.statement+
                '  </span>'+
                '</div>'+
                '<div style="float: none; clear: both;"></div>';
        };

        var display_beans_info = function() {
            var i;

            // 1. Theme style
            document.getElementById('beans-if-join').innerHTML = print_beans('?');
            var cta_list = document.getElementsByClassName('beans-cta-button');
            for (i = 0; i < cta_list.length; i++) {
                cta_list[i].style.backgroundColor = beans_card.style.primary_color;
                cta_list[i].style.color = beans_card.style.contrast_color;
            }

            // 2. Replace card attributes

            var beans_names = document.getElementsByClassName('beans-beans_name');
            if(beans_names && beans_names.length)
                for (i = 0; i < beans_names.length; i++)
                    beans_names[i].innerHTML = beans_card.beans_name;

            var company_names = document.getElementsByClassName('beans-company_name');
            if(company_names && company_names.length)
                for (i = 0; i < company_names.length; i++)
                    company_names[i].innerHTML = beans_card.company_name;

            var beans_rates = document.getElementsByClassName('beans-beans_rate');
            if(beans_rates && beans_rates.length)
                for (i = 0; i < beans_rates.length; i++)
                    beans_rates[i].innerHTML = beans_card.beans_rate +' '+beans_card.beans_name;

            var ccy_symbols = document.getElementsByClassName('beans-currency_symbol');
            if(ccy_symbols && ccy_symbols.length)
                for (i = 0; i < ccy_symbols.length; i++)
                    ccy_symbols[i].innerHTML = beans_card.currency.symbol;

            var websites = document.getElementsByClassName('beans-website');
            if(websites && websites.length)
                for (i = 0; i < websites.length; i++)
                    websites[i].innerHTML = beans_card.website;

            var card_urls = document.querySelectorAll('[href*="@card_url"]');
            if(card_urls && card_urls.length)
                for (i = 0; i < card_urls.length; i++)
                    card_urls[i].href = card_urls[i].getAttribute('href').replace(/@card_url/g, beans_card.url);

            var card_websites = document.querySelectorAll('[href*="@card_website"]');
            if(card_websites && card_websites.length)
                for (i = 0; i < card_websites.length; i++)
                    card_websites[i].href = card_websites[i].getAttribute('href').replace(/@card_website/g, beans_card.website);

            // Rules
            var beans_rules_list = document.getElementById('beans-rule-list');
            function insert_rule(rule) {
                var i;
                var line = document.createElement("div");
                line.className = "beans-rule-item beans-clearfix";
                line.innerHTML = print_rule(rule);
                beans_rules_list.appendChild(line);

                var helps = document.querySelectorAll('[beans-help*="'  + rule.uid+'"]');
                if(helps && helps.length){
                    for (i = 0; i < helps.length; i++)
                        helps[i].style.display = 'block';
                }

                var rule_beans = document.querySelectorAll('[beans-rule_beans*="'  + rule.uid+'"]');
                if(rule_beans && rule_beans.length){
                    for (i = 0; i < rule_beans.length; i++)
                        rule_beans[i].innerHTML = rule.beans + ' '+beans_card.beans_name;
                }

                var settings = document.querySelectorAll('[beans-rule_settings*="'  + rule.uid+'"]');
                if(settings && settings.length){
                    for (i = 0; i < settings.length; i++){
                        var attr = settings[i].getAttribute('beans-rule_settings').split(':')[2];
                        if(attr == 'minimum'){
                            settings[i].innerHTML = rule.settings[attr] || '0.01';
                        }
                        else if(attr == 'all_purchases_str'){
                            settings[i].innerHTML = rule.settings['all_purchases'] ? 'each' : 'the first';
                        }
                        else{
                            settings[i].innerHTML = rule.settings[attr];
                        }
                    }
                }

                if(rule.uid == 'beans:refer_friend_percent'){
                    document.getElementById('beans_refer_friend_percent_commission').innerHTML = parseInt(100.0*rule.beans/beans_card.beans_rate);
                }

            }
            function create_rules_list(rules) {
                rules.map(function(rule){
                    Beans.get({
                        method: 'rule/'+rule.id,
                        success: insert_rule
                    })
                })
            }
            Beans.get({
                method: 'rule',
                success: create_rules_list
            });

            // History & Balance
            function display_balance_history(account){

                // Account Balance
                document.getElementById('beans-account-balance').innerHTML = account.beans + ' ' + beans_card.beans_name;

                // Account History
                var beans_history_table = document.getElementById('beans-history-list');

                function insert_history_record(record) {
                    var line = document.createElement("div");
                    line.className = 'beans-history-entry beans-clearfix';
                    var date = new Date(record.created);
                    line.innerHTML = '<div class="beans-history-description">'  + record.description + '</div> ' +
                        '<div class="beans-history-date">' + date.toLocaleDateString() + '</div> ' +
                        '<div class="beans-history-beans">' + print_beans(record.delta) + '</div>';
                    beans_history_table.appendChild(line);
                }

                function create_history_table(records) {
                    records=records.sort(function(a, b){
                        var d1=new Date(a.created);
                        var d2=new Date(b.created);
                        return d2-d1;
                    }).slice(0,10);
                    records.map(insert_history_record)
                }

                Beans.get({
                    method: 'account/current/history',
                    success: create_history_table
                });

            }
            Beans.get({
                method: 'account/current',
                success: function (account) {
                    display_balance_history(account);
                    document.getElementById('beans-block-intro').style.display = 'none';
                    document.getElementById('beans-block-balance').style.display = 'block';
                    document.getElementById('beans-block-rules').style.display = 'block';
                    document.getElementById('beans-block-history').style.display = 'block';
                },
                error: function(error){
                    document.getElementById('beans-block-intro').style.display = 'block';
                    document.getElementById('beans-block-balance').style.display = 'none';
                    document.getElementById('beans-block-rules').style.display = 'block';
                    document.getElementById('beans-block-history').style.display = 'none';
                }
            });
        };

        Beans.get({
            method: 'card/current',
            success: function(data){
                beans_card=data;
                display_beans_info();
            }
        });

    </script>

</div>