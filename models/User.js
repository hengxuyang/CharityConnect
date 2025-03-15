const { v4: uuidv4 } = require('uuid');
const bcrypt = require('bcrypt');

// Define a constant salt rounds value
const SALT_ROUNDS = 10;

module.exports = (sequelize, DataTypes) => {
  const User = sequelize.define('User', {
    id: {
      type: DataTypes.UUID,
      defaultValue: () => uuidv4(),
      primaryKey: true
    },
    name: {
      type: DataTypes.STRING,
      allowNull: false
    },
    email: {
      type: DataTypes.STRING,
      allowNull: false,
      unique: true,
      validate: {
        isEmail: true
      }
    },
    password: {
      type: DataTypes.STRING,
      allowNull: false
    },
    role: {
      type: DataTypes.ENUM('admin', 'charity', 'public_user'),
      allowNull: false,
      defaultValue: 'public_user'
    },
    charity_id: {
      type: DataTypes.UUID,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      defaultValue: DataTypes.NOW
    }
  }, {
    timestamps: false,
    tableName: 'users',
    hooks: {
      beforeCreate: async (user) => {
        if (user.password) {
          // Use the constant salt rounds value directly
          user.password = await bcrypt.hash(user.password, SALT_ROUNDS);
        }
      },
      beforeUpdate: async (user) => {
        if (user.changed('password')) {
          // Use the constant salt rounds value directly
          user.password = await bcrypt.hash(user.password, SALT_ROUNDS);
        }
      }
    }
  });

  User.prototype.validPassword = async function(password) {
    return await bcrypt.compare(password, this.password);
  };

  User.associate = (models) => {
    User.belongsTo(models.Charity, {
      foreignKey: 'charity_id',
      as: 'charity'
    });
    User.hasMany(models.Donation, {
      foreignKey: 'donor_id',
      as: 'donations'
    });
    User.hasMany(models.Notification, {
      foreignKey: 'user_id',
      as: 'notifications'
    });
  };

  return User;
};
