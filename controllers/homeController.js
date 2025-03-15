// Home controller

// Render home page
exports.getHomePage = (req, res) => {
  res.render('index', {
    title: 'CharityConnect - Connecting Donors with Charities'
  });
};

// Render about page
exports.getAboutPage = (req, res) => {
  res.render('about', {
    title: 'About Us - CharityConnect'
  });
};

// Render contact page
exports.getContactPage = (req, res) => {
  res.render('contact', {
    title: 'Contact Us - CharityConnect'
  });
};
