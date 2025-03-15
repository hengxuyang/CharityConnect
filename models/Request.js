const { v4: uuidv4 } = require('uuid');

module.exports = (sequelize, DataTypes) => {
  const Request = sequelize.define('Request', {
    id: {
      type: DataTypes.UUID,
      defaultValue: () => uuidv4(),
      primaryKey: true
    },
    charity_id: {
      type: DataTypes.UUID,
      allowNull: false
    },
    item_id: {
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
    request_type: {
      type: DataTypes.ENUM('drop-off', 'pickup'),
      allowNull: false,
      defaultValue: 'drop-off'
    },
    drop_off_address: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    available_times: {
      type: DataTypes.DATE,
      allowNull: false
    },
    status: {
      type: DataTypes.ENUM('Pending', 'Fulfilled', 'Cancelled'),
      allowNull: false,
      defaultValue: 'Pending'
    },
    created_at: {
      type: DataTypes.DATE,
      defaultValue: DataTypes.NOW
    }
  }, {
    timestamps: false,
    tableName: 'requests'
  });

  Request.associate = (models) => {
    Request.belongsTo(models.Charity, {
      foreignKey: 'charity_id',
      as: 'charity'
    });
    Request.belongsTo(models.Item, {
      foreignKey: 'item_id',
      as: 'item'
    });
    Request.hasMany(models.Donation, {
      foreignKey: 'request_id',
      as: 'donations'
    });
    Request.hasMany(models.Notification, {
      foreignKey: 'request_id',
      as: 'notifications'
    });
  };

  return Request;
};
