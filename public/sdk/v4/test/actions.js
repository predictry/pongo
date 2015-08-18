var assert = require("assert");
var jsdom = require('mocha-jsdom');
var should = require('should');

describe('Array', function() {
    jsdom();
    describe('#indexOf()', function () {
      it('should return -1 when the value is not present', function () {
        assert.equal(-1, [1,2,3].indexOf(5));
        assert.equal(-1, [1,2,3].indexOf(0));
      });
    });
});


describe('ViewData', function() {
  jsdom();
  describe('#trac[data]', function() {
    if('should return this when the value is this', function() {

    });
  });
});
