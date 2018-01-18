var AhUtm = {
  recordUtmToCookie: function () {
    var utmFromQuery = this.buildUtmFromQuery();

    if (utmFromQuery) {
      Cookies.set('ahUtm', utmFromQuery, {domain: 'amazinghiring.ru'});
    } else {
      var referrer = this.getReferrer();
      var utmFromCookie = Cookies.getJSON('ahUtm');

      if (referrer && (!utmFromCookie || utmFromCookie.referrer)) {
        Cookies('ahUtm', referrer, {domain: 'amazinghiring.ru'});
      }
    }
  },

  getReferrer: function () {
    var referrer;

    if (document.referrer && document.referrer.indexOf('amazinghiring.') === -1) {
      referrer = {referrer: document.referrer};
    }

    return referrer;
  },

  buildUtmFromQuery: function () {
    var utm;
    var params = this.getUrlParams();

    if (params['utm_source'] && params['utm_medium']) {
      utm = {};
      $.each(params, function (key, value) {
        if (key.indexOf('utm_') === 0) {
          utm[key] = value;
        }
      });
    }

    return utm;
  },

  getUrlParams: function (prop) {
    var params = {};
    var search = decodeURIComponent(window.location.href.slice(window.location.href.indexOf('?') + 1));
    var definitions = search.split('&');

    definitions.forEach(function (val) {
      var parts = val.split('=', 2);
      params[parts[0]] = parts[1];
    });

    return (prop && prop in params) ? params[prop] : params;
  }
};

AhUtm.recordUtmToCookie();
