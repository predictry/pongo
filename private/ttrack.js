  var config_cookie_name_prefix = "_predictry_",
      config_cookie_disabled = false,
      config_cookie_path = "/",
      config_default_request_method = "POST",
      config_request_method = config_default_request_method,
      config_default_request_content_type = "application/x-www-form-urlencoded; charset=UTF-8",
      config_request_content_type = config_default_request_content_type,
      config_api_url = "https://api.predictry.com/",
      config_cf_trackings_url = "https://d1j642hg7oh3vx.cloudfront.net/",
      config_s3_resource_url = "https://s3-ap-southeast-1.amazonaws.com/predictry/",
      config_api_resources = ["actions", "users", "items", "carts", "cartlogs", "recommendation"],
      config_default_actions = ["view", "add_to_cart", "buy", "started_checkout", "started_payment", "check_delete_item", "delete_item", "custom"],
      config_session_cookie_timeout = 63072000000, // 2 years
      config_tracking_session_cookie_timeout = 1200000, //20 minutes
      config_do_not_track = false,
      config_s3_data_recommendation_path = "data/tenants/{tenant}/recommendations/",
      config_s3_data_items_path = "data/tenants/{tenant}/items/",
      config_s3_data_category_items_path = "data/tenants/{tenant}/categories/",
      config_default_s3_resource_ext = ".json",
      config_cls_prefix = "pry-",
      config_prefix_param = "p_";

