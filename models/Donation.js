const { v4: uuidv4 } = require('uuid');

module.exports = (sequelize, DataTypes) => {
  const Donation = sequelize.define('Donation', {
    id: {
      type: DataTypes.UUID,
      defaultValue: () => uuidv4(),
      primaryKey: true
    },
    donor_id: {
      type: DataTypes.UUID,
      allowNull: false
    },
    request_id: {
      type: DataTypes.UUID,
      allowNull: false
    },
    quantity: {
      type: DataTypes.INTEGER,
      allowNull: false,
      validate: {
        min: 1
      }
    },
    status: {
      type: DataTypes.ENUM('Pending', 'Delivered', 'Cancelled'),
      allowNull: false,
      defaultValue: 'Pending'
    },
    created_at: {
      type: DataTypes.DATE,
      defaultValue: DataTypes.NOW
    }
  }, {
    timestamps: false,
    tableName: 'donations'
  });

  Donation.associate = (models) => {
    Donation.belongsTo(models.User, {
      foreignKey: 'donor_id',
      as: 'donor'
    });
    Donation.belongsTo(models.Request, {
      foreignKey: 'request_id',
      as: 'request'
    });
    Donation.hasMany(models.Notification, {
      foreignKey: 'donation_id',
      as: 'notifications'
    });
  };

  return Donation;
};
