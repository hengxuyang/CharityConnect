const { v4: uuidv4 } = require('uuid');

module.exports = (sequelize, DataTypes) => {
  const Notification = sequelize.define('Notification', {
    id: {
      type: DataTypes.UUID,
      defaultValue: () => uuidv4(),
      primaryKey: true
    },
    user_id: {
      type: DataTypes.UUID,
      allowNull: false
    },
    request_id: {
      type: DataTypes.UUID,
      allowNull: true
    },
    donation_id: {
      type: DataTypes.UUID,
      allowNull: true
    },
    charity_id: {
      type: DataTypes.UUID,
      allowNull: true
    },
    type: {
      type: DataTypes.ENUM('request_update', 'donation_update', 'inventory_alert', 'thank_you_note'),
      allowNull: false
    },
    message: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    created_at: {
      type: DataTypes.DATE,
      defaultValue: DataTypes.NOW
    }
  }, {
    timestamps: false,
    tableName: 'notifications'
  });

  Notification.associate = (models) => {
    Notification.belongsTo(models.User, {
      foreignKey: 'user_id',
      as: 'user'
    });
    Notification.belongsTo(models.Request, {
      foreignKey: 'request_id',
      as: 'request'
    });
    Notification.belongsTo(models.Donation, {
      foreignKey: 'donation_id',
      as: 'donation'
    });
    Notification.belongsTo(models.Charity, {
      foreignKey: 'charity_id',
      as: 'charity'
    });
  };

  return Notification;
};
