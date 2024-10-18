;(() => {
    const { __ } = window.wp.i18n
    const { createElement } = window.wp.element;

    const cp = 'cryptopay';
    const cpLite = 'cryptopay-lite';

    const ReactElement = (type, props = {}, ...childs) => {
        return Object(createElement)(type, props, ...childs);
    }

    window.givewp.gateways.register({
        id: cp,
        async beforeCreatePayment(values) {
            if (values.firstName === 'error') {
                throw new Error('Failed in some way');
            }

            return {
                cryptoPayIntent: cp + '-intent',
            };
        },
        Fields() {
            return ReactElement("span", null, __("You can pay with supported networks and cryptocurrencies.", "cryptopay-gateway-for-givewp"));
        },
    });

    window.givewp.gateways.register({
        id: cpLite,
        async beforeCreatePayment(values) {
            if (values.firstName === 'error') {
                throw new Error('Failed in some way');
            }

            return {
                cryptoPayLiteIntent: cpLite + '-intent',
            };
        },
        Fields() {
            return ReactElement("span", null, __("You can pay with supported networks and cryptocurrencies.", "cryptopay-gateway-for-givewp"));
        },
    });
})();