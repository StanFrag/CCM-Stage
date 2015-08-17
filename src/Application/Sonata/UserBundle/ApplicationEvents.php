<?php

namespace Application\Sonata\UserBundle;

final class ApplicationEvents {
   // chaque constante correspond à un évènement
    const AFTER_REGISTER = "application_user_bundle.after_register";
    const AFTER_POPULATE = "application_user_bundle.after_populate";
}